<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DemolitionController extends AbstractController
{
    #[Route('/demolition', name: 'app_demolition')]
    public function index(): Response
    {
        return $this->render('demolition/index.html.twig', [
            'controller_name' => 'DemolitionController',
        ]);
    }
}
