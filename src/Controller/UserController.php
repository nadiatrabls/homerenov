<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Facture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AbonneRepository;
use App\Repository\FactureRepository;
use App\Repository\DevisRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserController extends AbstractController
{
    #[Route('/user/{id}/dashboard', name: 'user_dashboard')]
    public function dashboard(int $id, AbonneRepository $abonneRepository, FactureRepository $factureRepository, DevisRepository $devisRepository): Response
    {
        // Récupérer les informations de l'utilisateur connecté via son ID
        $user = $abonneRepository->find($id);

        // Vérifier si l'utilisateur existe
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Récupérer les factures et devis associés à l'utilisateur
        $factures = $factureRepository->findBy(['user' => $user]);
        $devis = $devisRepository->findBy(['user' => $user]);

        return $this->render('user/dashboard.html.twig', [
            'user' => $user,
            'factures' => $factures,
            'devis' => $devis,
        ]);
    }

    // Téléchargement d'une facture
    #[Route('/user/facture/{id}/telecharger', name: 'user_download_facture')]
    public function downloadFacture(Facture $facture): Response
    {
        $filePath = $this->getParameter('facture_directory') . '/' . $facture->getFichier();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier n\'existe pas.');
        }

        return $this->file($filePath, $facture->getFichier(), ResponseHeaderBag::DISPOSITION_INLINE);
    }

    // Téléchargement d'un devis
    #[Route('/user/devis/{id}/telecharger', name: 'user_download_devis')]
    public function downloadDevis(Devis $devis): Response
    {
        $filePath = $this->getParameter('devis_directory') . '/' . $devis->getFichier();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier n\'existe pas.');
        }

        return $this->file($filePath, $devis->getFichier(), ResponseHeaderBag::DISPOSITION_INLINE);
    }

    // Voir une facture
    #[Route('/user/facture/{id}', name: 'user_view_facture', methods: ['GET'])]
    public function viewFacture(Facture $facture): Response
    {
        return $this->render('user/view_facture.html.twig', [
            'facture' => $facture,
        ]);
    }

    // Voir un devis
    #[Route('/user/devis/{id}', name: 'user_view_devis', methods: ['GET'])]
    public function viewDevis(Devis $devis): Response
    {
        return $this->render('user/view_devis.html.twig', [
            'devis' => $devis,
        ]);
    }
//demande de facture
    #[Route('/user/{id}/demander-facture', name: 'user_request_facture', methods: ['GET', 'POST'])]
public function requestFacture(int $id, Request $request, AbonneRepository $abonneRepository, MailerInterface $mailer): Response
{
    // Récupérer l'utilisateur
    $user = $abonneRepository->find($id);

    // Vérifier si l'utilisateur existe
    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé.');
    }

    if ($request->isMethod('POST')) {
        // Traitement de la demande (par exemple envoyer un email)
        $message = $request->request->get('message');
        
        // Envoyer un email à l'administrateur (ou enregistrer dans une base de données, etc.)
        $email = (new Email())
            ->from($user->getEmail())
            ->to('admin@homerenov.com') // L'adresse email de l'admin
            ->subject('Demande de facture')
            ->text("L'utilisateur {$user->getNom()} {$user->getPrenom()} demande une facture. Message : {$message}");
        
        $mailer->send($email);

        // Ajouter un message flash pour informer l'utilisateur que sa demande a été envoyée
        $this->addFlash('success', 'Votre demande de facture a été envoyée.');

        return $this->redirectToRoute('user_dashboard', ['id' => $user->getId()]);
    }

    return $this->render('user/request_facture.html.twig', [
        'user' => $user,
    ]);
}
}
