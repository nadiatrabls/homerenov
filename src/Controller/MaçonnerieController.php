<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MaçonnerieController extends AbstractController
{
    #[Route('/maconnerie', name: 'app_maconnerie')]
    public function index(): Response
    {
        return $this->render('maçonnerie/index.html.twig', [
            'controller_name' => 'MaçonnerieController',
        ]);
    }
}
