<?php

namespace App\Controller;

use App\Entity\Abonne;
use App\Entity\DemandeFacture;
use App\Entity\Devis;
use App\Entity\Disponibilite;
use App\Entity\Facture;
use App\Entity\RendezVous;
use App\Form\FactureDemandeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AbonneRepository;
use App\Repository\FactureRepository;
use App\Repository\DevisRepository;
use App\Repository\DisponibiliteRepository;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class UserController extends AbstractController
{
    #[Route('/user/{id}/dashboard', name: 'user_dashboard')]
    public function dashboard(int $id, AbonneRepository $abonneRepository, FactureRepository $factureRepository, DevisRepository $devisRepository, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $userConnected = $security->getUser();

        // Vérifier si l'utilisateur connecté correspond à l'ID dans l'URL
        if ($userConnected->getId() !== $id) {
            // Si l'utilisateur connecté n'est pas celui de l'ID dans l'URL, rediriger ou afficher une erreur
            return $this->redirectToRoute('user_dashboard', ['id' => $userConnected->getId()]);
        }

        // Récupérer les informations de l'utilisateur via son ID
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

    #[Route('/user/{id}/factures', name: 'user_factures')]
    public function voirFactures(int $id, EntityManagerInterface $em): Response
    {
        $factures = $em->getRepository(Facture::class)->findBy(['user' => $id]);
    
        return $this->render('user/factures.html.twig', [
            'factures' => $factures,
            'user' => $em->getRepository(Abonne::class)->find($id),
        ]);
    }
    

    #[Route('/user/{id}/devis', name: 'user_devis')]
    public function devis(int $id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(Abonne::class)->find($id);
        $devis = $em->getRepository(Devis::class)->findBy(['user' => $user]);

        return $this->render('user/devis.html.twig', [
            'devis' => $devis,
            'user' => $user
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




// Demande de facture
#[Route('/user/{id}/demander-facture', name: 'demander_facture', methods: ['GET', 'POST'])]
public function demandeFacture(Request $request, MailerInterface $mailer): Response
{
    // Récupérer l'utilisateur connecté
    $user = $this->getUser();
    if (!$user) {
        // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
        return $this->redirectToRoute('app_login');
    }

    
    $form = $this->createFormBuilder()
        ->add('referenceChantier', TextType::class, [
            'label' => 'Référence du chantier',
            'attr' => ['placeholder' => 'Référence du chantier'],
        ])
        ->add('factureAuNomDe', TextType::class, [
            'label' => 'Facture au nom de',
            'attr' => ['placeholder' => 'Nom de la personne ou société'],
        ])
        ->add('message', TextareaType::class, [
            'label' => 'Message (facultatif)',
            'required' => false,
            'attr' => ['placeholder' => 'Ajoutez un message si nécessaire'],
        ])
        ->getForm();

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer les données du formulaire
        $data = $form->getData();

        // Envoi de l'email
        $email = (new TemplatedEmail())
            ->from(new Address('contact@homerenov91.fr', 'Homerenov 91 : demande de facture'))
            ->to('homerenovations91@gmail.com')
            ->subject('Nouvelle demande de facture')
            ->htmlTemplate("emails/index.html.twig")
            ->context(['data' => $data]); // Passez les données du formulaire

        $mailer->send($email);

        // Message de confirmation et redirection
        $this->addFlash('success', 'Votre demande de facture a été envoyée avec succès.');
        return $this->redirectToRoute('user_dashboard', ['id' => $user->getId()]);
    }

    // Passer l'utilisateur connecté au template
    return $this->render('user/request_facture.html.twig', [
        'form' => $form->createView(),
        'user' => $user,
    ]);
}




//prise des rdv 
#[Route('/user/{id}/rendezvous', name: 'user_rendezvous')]
public function rendezvous(int $id, EntityManagerInterface $em): Response
{
    $disponibilites = $em->getRepository(Disponibilite::class)->findAll();

    // Passer uniquement les créneaux disponibles au template
    $formattedDisponibilites = [];
    foreach ($disponibilites as $disponibilite) {
        $formattedDisponibilites[] = [
            'id' => $disponibilite->getId(),
            'date' => $disponibilite->getDate()->format('Y-m-d'),
            'heureDebut' => $disponibilite->getHeureDebut()->format('H:i'),
            'heureFin' => $disponibilite->getHeureFin()->format('H:i'),
            'disponible' => $disponibilite->isDisponible(),
        ];
    }

    return $this->render('user/rendezvous.html.twig', [
        'disponibilites' => $formattedDisponibilites,
        'id' => $id,
    ]);
}

//reservation du rdv

#[Route('/user/{userId}/rendezvous/{id}/reserver', name: 'user_reserver_rendezvous')]
public function reserverRendezVous(Disponibilite $disponibilite, Security $security, EntityManagerInterface $em, MailerInterface $mailer): Response
{
    $user = $security->getUser();

    // Vérifiez que $user est bien une instance de User
    if (!$user instanceof Abonne) {
        throw new \Exception('Utilisateur non valide');
    }

    // Vérifiez que la disponibilité est encore disponible
    if ($disponibilite->isDisponible()) {
        // Marquer comme réservé
        $disponibilite->setDisponible(false); // Indique que ce créneau n'est plus disponible
        $disponibilite->setClient($user); // Associe le client qui a réservé

        $em->flush();
        // Envoyer un email de notification à l'administrateur
        $adminEmail = (new TemplatedEmail())
            ->from(new Address('contact@homerenov91.fr', 'Notification Rendez-vous homerenov91'))
            ->to('homerenovations91@gmail.com') // Adresse email de l'administrateur
            ->subject('Nouveau Rendez-vous Réservé')
            ->htmlTemplate('emails/rendezvous_notification.html.twig')
            ->context([
                'disponibilite' => $disponibilite,
                'user' => $user
            ]);

        $mailer->send($adminEmail);

        // Envoyer un email de confirmation au client
        $confirmationEmail = (new TemplatedEmail())
            ->from(new Address('contact@homerenov91.fr', 'Homerenov91'))
            ->to($user->getEmail()) // Email de l'utilisateur
            ->subject('Confirmation de votre rendez-vous')
            ->htmlTemplate('emails/rendezvous_confirmation.html.twig')
            ->context([
                'disponibilite' => $disponibilite,
                'user' => $user
            ]);

        $mailer->send($confirmationEmail);
        // Afficher un message de succès ou rediriger
        $this->addFlash('success', 'Votre rendez-vous a été réservé avec succès.');
    } else {
        $this->addFlash('error', 'Ce créneau n\'est plus disponible.');
    }

    // Redirection avec l'ID de l'utilisateur
    return $this->redirectToRoute('user_dashboard', ['id' => $user->getId()]);
}




}
