<?php

namespace App\Controller;

use App\Entity\Chantier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChantiersController extends AbstractController
{
    #[Route('/chantier/{id}', name: 'chantier_show', methods: ['GET'])]
    public function show(Chantier $chantier): Response
    {
        // Affiche la page de détails pour un chantier
        return $this->render('chantiers/index.html.twig', [
            'chantier' => $chantier,
        ]);
    }
}
