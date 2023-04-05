<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Entity\BorrowBook;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    //Récupère tous les livres
    #[Route('/api/v1/books', name: 'app_books', methods:['GET'])]
    public function getAllBooks(BookRepository $booksRepository, SerializerInterface $serializer ): Response
    {
        $books = $booksRepository->findAll();
        $json = $serializer->serialize($books,'json', ['groups'=>'book:read']);
        $response = new response($json, 200, [
            "content-type" => "application/json"
        ] );
        
        return $response;
        
    }

    //Récupère un livre
    #[Route('/api/v1/book/{id}', name: 'app_book', methods:['GET'])]
    public function getBookById($id, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response

    {
        $booksRepository = $entityManager->getRepository(Book::class);
        $books = $booksRepository->findBy(['id' => $id]);
        $json = $serializer->serialize($books,'json', ['groups'=>'book:read']);

        $response = new Response($json, 200, [
            "Content-Type" =>"application/json"
        ]);
       
        
        return $response;
    }
    
    //Supprimer un livre
    #[Route('/api/v1/book/{id}', name: 'app_book_delete', methods:['DELETE'])]
    public function deleteBook(SerializerInterface $serializer, EntityManagerInterface $entityManager, $id):Response
    {
     
      $booksRepository = $entityManager->getRepository(Book::class);
      $books = $booksRepository->findOneBy(['id' => $id]);
      $json = $serializer->serialize($books,'json', ['groups'=>'book:read']);
      $entityManager ->remove($books);
      $entityManager->flush();

      return $this->json(null,Response::HTTP_NO_CONTENT);


    }

    //Ajouter un livre
    #[Route('/api/v1/books', name: 'app_books_post', methods:['POST'])]
    public function AddBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator):JsonResponse
     
    {
        $dataJson = $request->getContent();
        
            $book = $serializer->deserialize($dataJson,Book::class, 'json');
            
            $entityManager->persist($book);
            $entityManager->flush();
    
            return $this->json($book, Response::HTTP_CREATED,[],['groups' => 'book:read']);
       }




       /**
     * @Route("/api/users/{userId}/books/borrowed", name="api_book_borrowed_by_user", methods="GET")
     */
    public function getBooksBorrowedByUser(int $userId, BookRepository $bookRepository): JsonResponse
    {
        // Récupérer l'utilisateur à partir de son ID
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        // Vérifier si l'utilisateur existe
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer tous les livres empruntés par l'utilisateur
        $borrowedBooks = $bookRepository->findBorrowedBooksByUser($user);

        // Construire une réponse JSON
        $data = [
            'user' => $user->toArray(),
            'borrowed_books' => []
        ];

        foreach ($borrowedBooks as $book) {
            $data['borrowed_books'][] = $book->toArray();
        }

        return new JsonResponse($data);
    }



    /**
     * @Route("/api/v1/users/{idUser}/books/{idBook}", name="api_book_details", methods="GET")
     */
    public function bookDetails(Request $request, $idUser, $idBook)
    {
       

      // Récupérer le livre correspondant à l'ID
     $book = $this->getDoctrine()->getRepository(Book::class)->find($idBook);
     $user = $this->getDoctrine()->getRepository(User::class)->find($idUser);
    
      

        // Vérifier si le livre a été emprunté par l'utilisateur
        $borrowed = $this->getDoctrine()->getRepository(BorrowBook::class)->findOneBy(array(
            'user' => $user,
            'book' => $book,
            'returned' => false,
        ));
        dd($borrowed);

        // Vérifier si le livre a été rendu par l'utilisateur
        $returned = $this->getDoctrine()->getRepository(BorrowBook::class)->findOneBy(array(
            'user' => $user,
            'book' => $book,
            'returned' => true,
        ));

        // Construire la réponse JSON
        $response = array(
            'borrowed' => ($borrowed ? $borrowed->getStart()->format('Y-m-d H:i:s') : null),
            'returned' => ($returned ? $returned->getEnd()->format('Y-m-d H:i:s') : null),
        );

        return new JsonResponse($response);
    }
    }