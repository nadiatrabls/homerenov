<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IsolationController extends AbstractController
{
    #[Route('/isolation', name: 'app_isolation')]
    public function index(): Response
    {
        return $this->render('isolation/index.html.twig', [
            'controller_name' => 'IsolationController',
        ]);
    }
}
