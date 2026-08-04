<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Security\CsrfProtectedTrait;
use App\Security\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Routing\Attribute\Route;

class UsersController extends AbstractController
{
    use CsrfProtectedTrait;

    private const MIN_PASSWORD_LENGTH = 8;

    #[Route('api/show_users', name: 'show_users', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted(Roles::ROLE_ADMIN);
        $users = $entityManager->getRepository(Admin::class)->findAll();

        $result = array_map(
            static fn (Admin $user) => [
                'email' => $user->getEmail(),
                'role' => $user->getRoles(),
            ],
            $users
        );

        return new JsonResponse($result);
    }

    #[Route('api/change_password', name: 'changePassword', methods: ['POST'])]
    public function changePassword(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->assertValidCsrfToken($request);
        $request->isXmlHttpRequest();
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        $targetEmail = $parametersAsArray['users'] ?? null;

        if (!$this->isGranted(Roles::ROLE_ADMIN) && $this->getUser()->getUserIdentifier() !== $targetEmail) {
            throw $this->createAccessDeniedException('You may only change your own password.');
        }

        $user = $entityManager
           ->getRepository(Admin::class)
           ->findBy([
               'email' => $targetEmail]);

        if (!$user) {
            throw $this->createNotFoundException('No users found ');
        }

        $newPlainPassword = $parametersAsArray['newPass'] ?? '';
        if (mb_strlen($newPlainPassword) < self::MIN_PASSWORD_LENGTH) {
            throw new BadRequestHttpException('Password must be at least '.self::MIN_PASSWORD_LENGTH.' characters long.');
        }

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);
        $passwordHasher = $factory->getPasswordHasher('common');
        $newPassword = $passwordHasher->hash($newPlainPassword);
        $user[0]->setPassword($newPassword);
        $user[0]->setMustChangePassword(false);
        $entityManager->flush();

        $response = new Response();

        return $response;
    }

    #[Route('api/delete_user', name: 'deleteUser', methods: ['POST'])]
    public function deleteUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(Roles::ROLE_ADMIN);
        $this->assertValidCsrfToken($request);
        $request->isXmlHttpRequest();
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        $targetEmail = $parametersAsArray['userToDelete'] ?? null;

        if ($targetEmail === $this->getUser()->getUserIdentifier()) {
            throw new BadRequestHttpException('You cannot delete your own account.');
        }

        $user = $entityManager
           ->getRepository(Admin::class)
           ->findBy([
               'email' => $targetEmail]);

        if (!$user) {
            throw $this->createNotFoundException('No user found ');
        }

        if (in_array(Roles::ROLE_ADMIN, $user[0]->getRoles(), true)) {
            $allAdmins = $entityManager->getRepository(Admin::class)->findAll();
            $adminCount = count(array_filter($allAdmins, fn (Admin $a) => in_array(Roles::ROLE_ADMIN, $a->getRoles(), true)));
            if ($adminCount <= 1) {
                throw new BadRequestHttpException('Cannot delete the last remaining admin account.');
            }
        }

        $entityManager->remove($user[0]);
        $entityManager->flush();

        $response = new Response();

        return $response;
    }

    #[Route('api/create_user', name: 'createUser', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(Roles::ROLE_ADMIN);
        $this->assertValidCsrfToken($request);
        $request->isXmlHttpRequest();
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        $role = $parametersAsArray['role'] ?? null;
        if (!in_array($role, Roles::ASSIGNABLE, true)) {
            throw new BadRequestHttpException('Invalid role.');
        }

        if (mb_strlen($parametersAsArray['password'] ?? '') < self::MIN_PASSWORD_LENGTH) {
            throw new BadRequestHttpException('Password must be at least '.self::MIN_PASSWORD_LENGTH.' characters long.');
        }

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');
        $password = $passwordHasher->hash($parametersAsArray['password']);
        $user = new Admin();
        $user->setEmail($parametersAsArray['user']);
        $user->setPassword($password);
        $user->setRoles([$role]);

        $entityManager->persist($user);
        $entityManager->flush();

        $response = new Response();

        return $response;
    }
}
