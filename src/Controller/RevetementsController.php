<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RevetementsController extends AbstractController
{
    #[Route('/revetements', name: 'app_revetements')]
    public function index(): Response
    {
        return $this->render('revetements/index.html.twig', [
            'controller_name' => 'RevetementsController',
        ]);
    }
}
