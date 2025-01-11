<?php
namespace App\Controller;

use App\Repository\ChantierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RealisationsController extends AbstractController
{
    #[Route('/realisations', name: 'app_realisations')]
    public function index(ChantierRepository $chantierRepository, SluggerInterface $slugger): Response
    {
        // Récupérer tous les chantiers depuis la base de données
        $chantiers = $chantierRepository->findAll();

        // Générer le slug pour chaque chantier
        foreach ($chantiers as $chantier) {
            $chantier->slug = $slugger->slug($chantier->getNom())->lower();  // Création du slug
        }

        // Rendre la page avec les chantiers
        return $this->render('realisations/index.html.twig', [
            'chantiers' => $chantiers,
        ]);
    }
}
