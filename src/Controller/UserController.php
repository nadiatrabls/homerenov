<?php

namespace App\Controller;

use App\Entity\Abonne;
use App\Entity\Devis;
use App\Entity\Facture;
use App\Entity\RendezVous;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AbonneRepository;
use App\Repository\FactureRepository;
use App\Repository\DevisRepository;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
//  méthode demanderFacture avec l'envoi d'email
#[Route('/user/{id}/demander-facture', name: 'demander_facture', methods: ['GET', 'POST'])]
public function demanderFacture(Request $request, Abonne $user, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
{
    $form = $this->createFormBuilder()
        ->add('referenceChantier', TextType::class, [
            'label' => 'Référence du chantier',
            'required' => true,
            'attr' => ['placeholder' => 'Référence du chantier']
        ])
        ->add('factureAuNomDe', TextType::class, [
            'label' => 'Facture au nom de',
            'required' => true,
            'attr' => ['placeholder' => 'Nom de la personne ou société']
        ])
        ->add('message', TextareaType::class, [
            'label' => 'Message (facultatif)',
            'required' => false,
            'attr' => ['placeholder' => 'Ajoutez un message si nécessaire']
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $referenceChantier = $form->get('referenceChantier')->getData();
        $factureAuNomDe = $form->get('factureAuNomDe')->getData();
        $message = $form->get('message')->getData();

        try {
            // Envoyer l'email
            $email = (new Email())
                ->from($user->getEmail())  // Utilisez l'email du client
                ->to('homerenovations91@gmail.com')  // Email de l'admin
                ->subject('Demande de facture de la part de ' . $user->getNom())
                ->html(
                    '<p>Une nouvelle demande de facture a été faite par ' . $user->getPrenom() . ' ' . $user->getNom() . '</p>' .
                    '<p>Référence du chantier : ' . $referenceChantier . '</p>' .
                    '<p>Facture au nom de : ' . $factureAuNomDe . '</p>' .
                    '<p>Message : ' . ($message ?: 'Pas de message fourni') . '</p>'
                );

            $mailer->send($email);

            // Message de succès
            $this->addFlash('success', 'Votre demande de facture a bien été envoyée.');

        } catch (\Exception $e) {
            // Message d'erreur si l'envoi échoue
            $this->addFlash('error', 'Votre message n\'a pas pu être envoyé. Veuillez réessayer plus tard.');
        }

        return $this->redirectToRoute('user_dashboard', ['id' => $user->getId()]);
    }

    return $this->render('user/request_facture.html.twig', [
        'form' => $form->createView(),
        'user' => $user, // Ajouter l'utilisateur ici pour le template
    ]);
}
//voir les rdv disponibles
#[Route('/rendezvous', name: 'user_rendezvous_list')]
public function listRendezVous(RendezVousRepository $rendezVousRepository): Response
{
    // Récupérer uniquement les rendez-vous disponibles
    $rendezVousDispos = $rendezVousRepository->findBy(['disponible' => true]);

    return $this->render('rendezvous/list.html.twig', [
        'rendezvous' => $rendezVousDispos,
    ]);
}
//reserver un rdv

#[Route('/rendezvous/{id}/reserver', name: 'user_rendezvous_reserver')]
public function reserver(RendezVous $rendezVous, EntityManagerInterface $em, Security $security): Response
{
    $user = $security->getUser();

    if ($rendezVous->getDisponible()) {
        $rendezVous->setDisponible(false);
        $rendezVous->setClient($user); // Associe l'utilisateur au rendez-vous
        $em->flush();

        $this->addFlash('success', 'Votre réservation a été prise en compte.');
    } else {
        $this->addFlash('danger', 'Ce créneau est déjà réservé.');
    }

    return $this->redirectToRoute('user_rendezvous_list');
}



}
