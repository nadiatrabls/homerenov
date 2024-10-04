<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CloisonsController extends AbstractController
{
    #[Route('/cloisons', name: 'app_cloisons')]
    public function index(): Response
    {
        return $this->render('cloisons/index.html.twig', [
            'controller_name' => 'CloisonsController',
        ]);
    }
}
