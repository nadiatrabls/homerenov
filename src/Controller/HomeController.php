<?php

namespace App\Controller;

use App\Repository\ChantierRepository; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ChantierRepository $chantierRepository): Response

    {
        // Récupérer les chantiers avec les IDs 5, 8 et 14
        $chantiersSpecifiques = $chantierRepository->findBy(['id' => [5, 8, 14]]);

        // Récupérer les 8 premiers chantiers dans la base de données
        $chantiers = $chantierRepository->findBy([], null, 8);

        return $this->render('home/index.html.twig', [
            'chantiers' => $chantiers,
            'chantiersSpecifiques' => $chantiersSpecifiques,
        ]);
    }
}

