<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChantiersController extends AbstractController
{
    #[Route('/chantiers', name: 'app_chantiers')]
    public function index(): Response
    {
        return $this->render('chantiers/index.html.twig', [
            'controller_name' => 'ChantiersController',
        ]);
    }
}
