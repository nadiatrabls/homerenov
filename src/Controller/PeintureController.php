<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PeintureController extends AbstractController
{
    #[Route('/peinture', name: 'app_peinture')]
    public function index(): Response
    {
        return $this->render('peinture/index.html.twig', [
            'controller_name' => 'PeintureController',
        ]);
    }
}
