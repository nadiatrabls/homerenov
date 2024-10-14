<?php

namespace App\Controller;
use App\Repository\ChantierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RealisationsController extends AbstractController
{
    #[Route('/realisations', name: 'app_realisations')]
    public function index(ChantierRepository $chantierRepository): Response
    {
        // Récupérer tous les chantiers depuis la base de données
        $chantiers = $chantierRepository->findAll();

        // Rendre la page avec les chantiers
        return $this->render('realisations/index.html.twig', [
            'chantiers' => $chantiers,
        ]);
    }
}
