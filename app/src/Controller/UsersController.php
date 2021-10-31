<?php

namespace App\Controller;

use App\Entity\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UsersController extends AbstractController
{
    /**
     * @Route("api/show_users", name="show_users")
     */
    public function show(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
        $data = array();
        $email = array();
      //  $role = array();
        $users = $this->getDoctrine()
            ->getRepository(Admin::class)
            ->findAll();

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


    /**
     * @Route("api/change_password", name="changePassword")
     * @param Request $request
     * @param Request $newsPasswordRepeat
     */
    public function changePassword(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $this->getDoctrine()->getManager();
        $request->isXmlHttpRequest(); 
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        
         $user = $this->getDoctrine()
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
        // Retrieve the right password hasher by its name
        $passwordHasher = $factory->getPasswordHasher('common'); 
        // Hash a plain password
        $newPassword = $passwordHasher->hash($parametersAsArray['newPass']);
        $user[0]->setPassword($newPassword);
        $entityManager->flush();

        $response = new Response();
        return $response; 
    }  
    
    /**
     * @Route("api/delete_user", name="deleteUser")
     * @param Request $request
     * @param Request $user
     */
    public function deleteUser(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $this->getDoctrine()->getManager();
        $request->isXmlHttpRequest(); 
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        
         $user = $this->getDoctrine()
            ->getRepository(Admin::class)
            ->findBy([
                'email' => $parametersAsArray['userToDelete']]); 

        if (!$user) {
                    throw $this->createNotFoundException(
                        'No user found '
                    );
        }

      //  $userId = $user[0]->getId();

        $entityManager->remove($user[0]);
        $entityManager->flush();

        $response = new Response();
        return $response; 
    } 
    
    /**
     * @Route("api/create_user", name="createUser")
     * @param Request $request
     */
    public function createUser(Request $request): Response
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
        // Retrieve the right password hasher by its name
        $passwordHasher = $factory->getPasswordHasher('common'); 
        $password = $passwordHasher->hash($parametersAsArray['password']);
        $entityManager = $this->getDoctrine()->getManager();
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