<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class UserController extends AbstractController
{
    //Récupère tous les utilisateurs
    #[Route('/api/v1/users', name: 'app_users', methods:['GET'])]
    public function getAllUsers(UserRepository $usersRepository, SerializerInterface $serializer ): Response
    {
        $users = $usersRepository->findAll();
        $json = $serializer->serialize($users,'json', ['groups'=>'user:read']);
        $response = new response($json, 200, [
            "content-type" => "application/json"
        ] );
        
        return $response;
        
    }

    //Récupère un utilisateurs par son ID
    #[Route('/api/v1/user/{id}', name: 'app_user_id', methods:['GET'])]
    public function getUserById($id, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response

    {
        $usersRepository = $entityManager->getRepository(User::class);
        $users = $usersRepository->findBy(['id' => $id]);
        $json = $serializer->serialize($users,'json', ['groups'=>'user:read']);

        $response = new Response($json, 200, [
            "Content-Type" =>"application/json"
        ]);
       
        
        return $response;
    }
    //Récupère un utilisateurs par son UUID
    #[Route('/api/v1/user/{uuid}', name: 'app_user_uuid', methods:['GET'])]
    public function getUserByUuid($uuid, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response

    {
        $usersRepository = $entityManager->getRepository(User::class);
        $users = $usersRepository->findBy(['uuid' => $uuid]);
        $json = $serializer->serialize($users,'json', ['groups'=>'user:read']);

        $response = new Response($json, 200, [
            "Content-Type" =>"application/json"
        ]);
       
        
        return $response;
    }
    
    //Supprimer un utilisateur
    #[Route('/api/v1/user/{id}', name: 'app_user_delete', methods:['DELETE'])]
    public function deleteUser(SerializerInterface $serializer, EntityManagerInterface $entityManager, $id):Response
    {
     
      $usersRepository = $entityManager->getRepository(User::class);
      $users = $usersRepository->findOneBy(['id' => $id]);
      $json = $serializer->serialize($users,'json', ['groups'=>'user:read']);
      $entityManager ->remove($users);
      $entityManager->flush();

      return $this->json(null,Response::HTTP_NO_CONTENT);


    }

    //Ajouter un utilisateur
    #[Route('/api/v1/users', name: 'app_users_post', methods:['POST'])]
    public function AddUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator):JsonResponse
     
    {
        $dataJson = $request->getContent();
        
            $user = $serializer->deserialize($dataJson,User::class, 'json');
            
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->json($user, Response::HTTP_CREATED,[],['groups' => 'user:read']);
       }

    #[Route('/api/v1/user/login', name: 'getUserBy_uuid', methods:["POST"])]
    public function loginByUuid(UserRepository $userRepository, SerializerInterface $serializer, Request $request): Response
    {
        //method with the symfonyRequest-bundle
        $uuid = $request->get("uuid");
        try {
            $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(["uuid" => $uuid]);
            if (!$users) {
                return $this->json(["error" => " User not found"], 200);
            }

            $json = $serializer->serialize($users, 'json', ['groups' => 'user:read']);
            $response = new Response($json, 200, ["Content-Type" => "application/json"]);
            return $response;
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'staut' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
}