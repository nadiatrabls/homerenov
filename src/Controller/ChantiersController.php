<?php
// src/Controller/ChantierController.php
namespace App\Controller;

use App\Repository\ChantierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChantiersController extends AbstractController
{
    /**
     * @Route("/chantier/{id}/{slug}", name="chantier_show")
     */
    #[Route("/chantier/{id}/{slug}", name: "chantier_show")]
    public function show($id, $slug, ChantierRepository $chantierRepository)
    {
        // Récupération du chantier par ID (ou par slug si nécessaire)
        $chantier = $chantierRepository->findOneBy(['id' => $id]);

        if (!$chantier) {
            throw $this->createNotFoundException('Le chantier n\'existe pas.');
        }

        // // Vérification du slug
        // if ($slug !== $chantier->getSlug()) {
        //     // Si le slug ne correspond pas, on redirige vers la bonne URL
        //     return $this->redirectToRoute('chantier_show', [
        //         'id' => $chantier->getId(),
        //         'slug' => $chantier->getSlug()
        //     ]);
        // }

        return $this->render('chantiers/index.html.twig', [
            'chantier' => $chantier,
        ]);
    }
}
