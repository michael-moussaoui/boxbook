<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
 
class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
       // On instancie la classe user
       $user = new User();
        

       // On instancie le formulaire avec la méthode createForm
       $form = $this->createForm(RegisterType::class, $user);

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {

           // $form->getData() holds the submitted values
           $user = $form->getData();
           $plaintextPassword = $user->getPassword();

           // hash the password (based on the security.yaml config for the $user class)
           $hashedPassword = $passwordHasher->hashPassword(
           $user,
           $plaintextPassword
       );
           
           $user->setPassword($hashedPassword);

           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->persist($user);
           $entityManager->flush();


           // ... perform some action, such as saving the task to the database

           return $this->redirectToRoute('app_login');

       }
       return $this->renderForm('register/index.html.twig', [
           'form' => $form,
       ]);
   }
}
