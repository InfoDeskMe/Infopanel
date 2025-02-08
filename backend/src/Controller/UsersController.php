<?php

namespace App\Controller;

use App\Entity\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Doctrine\ORM\EntityManagerInterface;

class UsersController extends AbstractController
{
    #[Route('api/show_users', name: 'show_users')]
    public function show(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
        $data = array();
        $email = array();        
        $users = $entityManager->getRepository(Admin::class)->findAll();
            
        foreach ($users as $value) {
            $email[] = $value->getEmail();
            $role[] = json_encode($value->getRoles());

            $json_arr[] = array(
                "email" => $value->getEmail(),
                "role" => json_encode($value->getRoles())
             );
        }
  
        
        $json = json_encode($json_arr);

        if (!$users) {
            throw $this->createNotFoundException(
                'No users found '
            );
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($json, JSON_PRETTY_PRINT ));
        return $response;
        
    }

    #[Route('api/change_password', name: 'changePassword')]
    public function changePassword(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $request->isXmlHttpRequest(); 
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        
         $user = $entityManager
            ->getRepository(Admin::class)
            ->findBy([
                'email' => $parametersAsArray['users']]); 

        if (!$user) {
                    throw $this->createNotFoundException(
                        'No users found '
                    );
        }

        $userId = $user[0]->getId();

       // now we have a user ID and need to change the password. 
       // $newPassword = $passwordHasher->hash($parametersAsArray['newPass']);
        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);
        $passwordHasher = $factory->getPasswordHasher('common'); 
        $newPassword = $passwordHasher->hash($parametersAsArray['newPass']);
        $user[0]->setPassword($newPassword);
        $entityManager->flush();

        $response = new Response();
        return $response; 
    }  
    
    #[Route('api/delete_user', name: 'deleteUser')]
    public function deleteUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $request->isXmlHttpRequest(); 
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        
         $user = $entityManager
            ->getRepository(Admin::class)
            ->findBy([
                'email' => $parametersAsArray['userToDelete']]); 

        if (!$user) {
                    throw $this->createNotFoundException(
                        'No user found '
                    );
        }

        $entityManager->remove($user[0]);
        $entityManager->flush();

        $response = new Response();
        return $response; 
    } 
    
    #[Route('api/create_user', name: 'createUser')]
    public function createUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $request->isXmlHttpRequest();
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
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
        $user->setRoles(['role' => $parametersAsArray['role']]);

        $entityManager->persist($user);
        $entityManager->flush();

        $response = new Response();
        return $response; 
    } 
}