<?php



namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'sitemap', methods: ['GET'])]
    public function index(): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/sitemap.xml';

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier sitemap.xml est introuvable.');
        }

        return new Response(
            file_get_contents($filePath),
            200,
            ['Content-Type' => 'application/xml']
        );
    }
}
