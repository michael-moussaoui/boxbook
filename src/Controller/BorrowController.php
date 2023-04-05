<?php

namespace App\Controller;

use DateTime;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\Borrow;
use DateTimeImmutable;
use App\Repository\BorrowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BorrowController extends AbstractController
{
    #[Route('api/v1/borrows', name: 'app_borrows', methods:["GET"])]
    public function index(BorrowRepository $borrowRepository): Response
    {
        //Récupère tous les emprunts
        return $this->json($borrowRepository->findAll(),  Response::HTTP_OK,[], ['groups'=> 'borrow:read']);
    }

    
    #[Route("api/v1/borrowings", name: "app_borrowing", methods:["POST"])]
     public function createBorrowing(BorrowRepository $borrowRepository, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent(), true);
    $borrow = new Borrow();
    
    $user = $entityManager->getRepository(User::class)->find($data['user_id']);
    $book = $entityManager->getRepository(Book::class)->find($data['book_id']);
    

    $borrow->setUser($user);
    $borrow->setBook($book);
    $borrow->setBorrowAt(new DateTimeImmutable());
    $book->setIsAvailable(false);

    $entityManager->persist($borrow);
    $entityManager->flush();

    $json = $serializer->serialize($borrow, 'json', [
        'groups' => ['borrow:read'],
    ]);

    return new Response($json, Response::HTTP_CREATED, [
        'Content-Type' => 'application/json'
    ]);
    //     //  $entityManager = $this->getDoctrine()->getManager();
    //     //  $data = json_decode($request->getContent(), true);
    //     // $dataJson = $request->getContent();
    //     //  dd($dataJson);
    //     // $borrow = $serializer->deserialize($dataJson,Borrow::class, 'json');
        
    //     // dd($borrow);
         

    //     // $user = $entityManager->getRepository(User::class)->find($data['user_id']);
    //     // $book = $entityManager->getRepository(Book::class)->find($data['book_id']);

    //     // $borrows = $borrowRepository->findAll();
    //     // $json = $serializer->serialize($borrows, 'json', [
    //     //     'groups' => ['borrow:read'],
    //     // ]);

    //     $idUser = $request->get('user_id');
    //     // dd($idUser);
    //     // $usersRepository = $entityManager->getRepository(User::class);
    //     // $user = $usersRepository->findOneBy(['id' => $idUser]);
    //     $json = $serializer->deserialize($idUser,User::class ,'json');
    //     // dd($json);

        

    //     $idBook = $request->get('book_id');
    //     dd($idBook);
    //     // $booksRepository = $entityManager->getRepository(Book::class);
    //     // $book = $booksRepository->findOneBy(['id' => $idBook]);
    //     $json = $serializer->deserialize($idBook, Book::class,'json' );

       

    // //     // Récupérer l'utilisateur correspondant à l'ID dans les paramètres de la requête
    // //  $idUser = $request->get('user');
    // //  $user = $this->getDoctrine()->getRepository(User::class)->find($idUser);
    // //  // Récupérer l'utilisateur correspondant à l'ID dans les paramètres de la requête
    // //  $idBook = $request->get('book');
    // //  $book = $this->getDoctrine()->getRepository(User::class)->find($idBook);
        
    // //   dd($book);
    //     $borrow = new Borrow();
    //     $borrow->setBook();
    //     // dd($borrow);
    //     $borrowing->setUser($user);
    //     $borrowing->setBorrowAt(new DateTimeImmutable());
    //     $book->setIsAvailable(false);
    //     $entityManager->persist($borrowing);
    //     $entityManager->flush();

    //     return $this->json($borrowing, Response::HTTP_CREATED,[], ['groups'=>'borrow:read']);
    }


      #[Route("api/v1/borrowings/{id}", methods:["PUT"])]
      #[ParamConverter("borrow", class:"App\Entity\Borrow")]
     
    public function updateBorrowing(Request $request, Borrow $borrow, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {

        $borrow->setReturnAt(new DateTimeImmutable());
    $borrow->getBook()->setIsAvailable(true);

    $entityManager->flush();

    $json = $serializer->serialize($borrow, 'json', [
        'groups' => ['borrow:read'],
    ]);

    return new Response($json, Response::HTTP_OK, [
        'Content-Type' => 'application/json'
    ]);
        // $json = $serializer->serialize($borrowing, 'json', [
        //     'groups' => ['borrow:read'],
        // ]);
        // $entityManager = $this->getDoctrine()->getManager();
        // $data = json_decode($request->getContent(), true);

        // if (isset($data['returned_at'])) {
        //     $borrowing->setBorrowReturnAt(new DateTimeImmutable($data['returned_at']));
        // } else {
        //     $borrowing->setBorrowReturnAt(null);
        // }

        // $entityManager->flush();

        // return $this->json($borrowing, Response::HTTP_OK);
    }
}
