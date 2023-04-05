<?php

namespace App\Controller;

use App\Repository\BoxBookRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BoxBookController extends AbstractController
{
    #[Route('/api/v1/boxbook', name: 'app_box_book', methods:['GET'])]
    public function index(BoxBookRepository $boxBookRepository): Response
     //Récupère tous les boxbooks
     {
        return $this->json($boxBookRepository->findAll(),  Response::HTTP_OK,[], ['groups'=> 'box:read']);
      
    }
}
