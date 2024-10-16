<?php

namespace App\Controller;

use App\Entity\Abonne; // Remplace par l'entité utilisée pour les abonnés
use App\Entity\Chantier;
use App\Entity\Devis;
use App\Entity\Disponibilite;
use App\Entity\Facture;
use App\Entity\RendezVous;
use App\Form\AbonneType; // Remplace par le type de formulaire pour les abonnés
use App\Form\ChantierType;
use App\Form\DevisType;
use App\Form\DisponibiliteType;
use App\Form\FactureType;
use App\Form\RendezVousType;
use App\Repository\AbonneRepository;
use App\Repository\DevisRepository;
use App\Repository\DisponibiliteRepository;
use App\Repository\FactureRepository;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\String\Slugger\SluggerInterface;



class AdminController extends AbstractController
{
    /*******************crud user  ****************************/
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(AbonneRepository $abonneRepository): Response
    {
        // Récupérer tous les utilisateurs pour les afficher
        $users = $abonneRepository->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/{id}', name: 'user_show', methods: ['GET', 'POST'])]
public function show(Request $request, Abonne $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    // Créer une nouvelle entité Facture et Devis
    $facture = new Facture();
    $facture->setUser($user);

    $devis = new Devis();
    $devis->setUser($user);

    // Créer les formulaires
    $formFacture = $this->createForm(FactureType::class, $facture);
    $formDevis = $this->createForm(DevisType::class, $devis);

    // Traiter les soumissions de formulaire si nécessaire
    $formFacture->handleRequest($request);
    $formDevis->handleRequest($request);

    if ($formFacture->isSubmitted() && $formFacture->isValid()) {
        /** @var UploadedFile $file */
        $file = $formFacture->get('fichier')->getData();
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            try {
                $file->move($this->getParameter('facture_directory'), $newFilename);
            } catch (FileException $e) {
                return new Response('Erreur lors de l\'upload du fichier.');
            }
            $facture->setFichier($newFilename);
        }
        $entityManager->persist($facture);
        $entityManager->flush();

        // Redirection après la soumission de la facture
        return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
    }

    if ($formDevis->isSubmitted() && $formDevis->isValid()) {
        /** @var UploadedFile $file */
        $file = $formDevis->get('fichier')->getData();
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            try {
                $file->move($this->getParameter('devis_directory'), $newFilename);
            } catch (FileException $e) {
                return new Response('Erreur lors de l\'upload du fichier.');
            }
            $devis->setFichier($newFilename);
        }
        $entityManager->persist($devis);
        $entityManager->flush();

        // Redirection après la soumission du devis
        return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
    }

    // Passer les formulaires et l'utilisateur à la vue
    return $this->render('admin/show.html.twig', [
        'user' => $user,
        'form_facture' => $formFacture->createView(),
        'form_devis' => $formDevis->createView(),
    ]);
}

    #[Route('/admin/user/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, Abonne $abonne, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonne->getId(), $request->request->get('_token'))) {
            $entityManager->remove($abonne);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_dashboard');
    }

/*************************crud devis  ********************************/
//crée un devis
 /**
 * @Route("/admin/user/{id}/devis/new", name="user_devis_new", methods={"GET", "POST"})
 */
public function createDevis(Request $request, Abonne $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $devis = new Devis();
    $devis->setUser($user);

    $form = $this->createForm(DevisType::class, $devis);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gérer le fichier PDF du devis
        /** @var UploadedFile $fichierFile */
        $fichierFile = $form->get('fichier')->getData();

        if ($fichierFile) {
            // Générez un nom de fichier unique
            $originalFilename = pathinfo($fichierFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$fichierFile->guessExtension();

            try {
                // Déplacer le fichier dans le répertoire défini pour les devis
                $fichierFile->move(
                    $this->getParameter('devis_directory'), // Répertoire de stockage
                    $newFilename
                );
            } catch (FileException $e) {
                // Gérer l'erreur si le fichier ne peut pas être uploadé
            }

            // Enregistrer le nom du fichier dans l'entité `Devis`
            $devis->setFichier($newFilename);
        }

        $entityManager->persist($devis);
        $entityManager->flush();

        return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
    }

    return $this->render('admin/new_devis.html.twig', [
        'form' => $form->createView(),
        'user' => $user,
    ]);
}
//voir devis 
#[Route('/admin/user/{userId}/devis/{devisId}', name: 'view_devis', methods: ['GET'])]
public function viewDevis(int $userId, int $devisId, DevisRepository $devisRepository): Response
{
    $devis = $devisRepository->find($devisId);
    if (!$devis) {
        throw $this->createNotFoundException('Devis non trouvé');
    }

    return $this->render('admin/view_devis.html.twig', [
        'devis' => $devis,
    ]);
}
// edit devis

#[Route('/admin/user/{userId}/devis/{devisId}/edit', name: 'edit_devis', methods: ['GET', 'POST'])]
public function editDevis(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, $devisId): Response
{
    // Récupération du devis par son ID
    $devis = $entityManager->getRepository(Devis::class)->find($devisId);

    if (!$devis) {
        throw $this->createNotFoundException('Le devis n\'existe pas.');
    }

    // Création du formulaire
    $form = $this->createForm(DevisType::class, $devis);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gérer le fichier PDF du devis
        /** @var UploadedFile $file */
        $file = $form->get('fichier')->getData();
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            
            try {
                $file->move($this->getParameter('devis_directory'), $newFilename);
            } catch (FileException $e) {
                return new Response('Erreur lors de l\'upload du fichier.');
            }

            $devis->setFichier($newFilename);
        }

        // Mise à jour du devis en base de données
        $entityManager->flush();

        return $this->redirectToRoute('user_show', ['id' => $devis->getUser()->getId()]);
    }

    return $this->render('admin/edit_devis.html.twig', [
        'form_devis' => $form->createView(),
        'devis' => $devis,
    ]);
}
//delete devis 
#[Route('/admin/user/{userId}/devis/{devisId}/delete', name: 'delete_devis', methods: ['POST'])]
 public function deleteDevis(int $userId, int $devisId, Request $request, EntityManagerInterface $entityManager, DevisRepository $devisRepository): Response
 {
     $devis = $devisRepository->find($devisId);
     if (!$devis) {
         throw $this->createNotFoundException('Devis non trouvé');
     }

     if ($this->isCsrfTokenValid('delete' . $devis->getId(), $request->request->get('_token'))) {
         $entityManager->remove($devis);
         $entityManager->flush();
     }

     return $this->redirectToRoute('user_show', ['id' => $userId]);
 }
//upload devis
#[Route('/admin/user/{id}/devis/upload', name: 'upload_devis', methods: ['GET', 'POST'])]
public function uploadDevis(Request $request, Abonne $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $devis = new Devis();
    $devis->setUser($user);

    $form = $this->createForm(DevisType::class, $devis);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gérer le fichier PDF du devis
        /** @var UploadedFile $file */
        $file = $form->get('fichier')->getData();

        if ($file) {
            // Générer un nom de fichier unique
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('devis_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                return new Response('Erreur lors de l\'upload du fichier.');
            }

            $devis->setFichier($newFilename);
        }

        $entityManager->persist($devis);
        $entityManager->flush();

        return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
    }

    return $this->render('admin/show.html.twig', [
        'user' => $user,
        'form_facture' => $this->createForm(FactureType::class, new Facture())->createView(),
        'form_devis' => $form->createView(),
    ]);
}
//telecharger devis 
#[Route('/admin/devis/{id}/telecharger', name: 'download_devis', methods: ['GET'])]
public function downloadDevis(Devis $devis): Response
{
    $filePath = $this->getParameter('devis_directory') . '/' . $devis->getFichier();

    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('Le fichier n\'existe pas.');
    }

    return $this->file($filePath, $devis->getFichier(), ResponseHeaderBag::DISPOSITION_INLINE);
}

/****************************crud factures************************ */
//crée facture
/**
 * @Route("/admin/user/{id}/facture/new", name="user_facture_new", methods={"GET", "POST"})
 */
public function createFacture(Request $request, Abonne $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $facture = new Facture();
    $facture->setUser($user);

    $form = $this->createForm(FactureType::class, $facture);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gérer le fichier PDF de la facture
        /** @var UploadedFile $fichierFile */
        $fichierFile = $form->get('fichier')->getData();

        if ($fichierFile) {
            $originalFilename = pathinfo($fichierFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$fichierFile->guessExtension();

            try {
                $fichierFile->move(
                    $this->getParameter('facture_directory'), // Répertoire de stockage
                    $newFilename
                );
            } catch (FileException $e) {
                // Gérer l'erreur si le fichier ne peut pas être uploadé
            }

            // Enregistrer le nom du fichier dans l'entité `Facture`
            $facture->setFichier($newFilename);
        }

        $entityManager->persist($facture);
        $entityManager->flush();

        return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
    }

    return $this->render('admin/new_facture.html.twig', [
        'form' => $form->createView(),
        'user' => $user,
    ]);
}


//voir facture
#[Route('/admin/user/{userId}/facture/{factureId}', name: 'view_facture', methods: ['GET'])]
public function viewFacture(int $userId, int $factureId, FactureRepository $factureRepository): Response
{
    $facture = $factureRepository->find($factureId);
    if (!$facture) {
        throw $this->createNotFoundException('Facture non trouvée');
    }

    return $this->render('admin/view_facture.html.twig', [
        'facture' => $facture,
    ]);
}
//edit facture
#[Route('/admin/user/{userId}/facture/{factureId}/edit', name: 'edit_facture', methods: ['GET', 'POST'])]
public function editFacture(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, $factureId): Response
{
    $facture = $entityManager->getRepository(Facture::class)->find($factureId);

    if (!$facture) {
        throw $this->createNotFoundException('La facture n\'existe pas.');
    }

    $form = $this->createForm(FactureType::class, $facture);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gestion du nouveau fichier PDF s'il a été uploadé
        /** @var UploadedFile $file */
        $file = $form->get('fichier')->getData();
        if ($file) {
            // Gérer le remplacement du fichier existant
            if ($facture->getFichier()) {
                $oldFile = $this->getParameter('facture_directory') . '/' . $facture->getFichier();
                if (file_exists($oldFile)) {
                    unlink($oldFile); // Supprime l'ancien fichier
                }
            }

            // Gérer le nouvel upload
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('facture_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                return new Response('Erreur lors de l\'upload du fichier.');
            }

            $facture->setFichier($newFilename); // Met à jour le nouveau fichier dans l'entité
        }

        $entityManager->flush();

        return $this->redirectToRoute('user_show', ['id' => $facture->getUser()->getId()]);
    }

    return $this->render('admin/edit_facture.html.twig', [
        'form_facture' => $form->createView(),
        'facture' => $facture,
    ]);
}

//delete facture
#[Route('/admin/user/{userId}/facture/{factureId}/delete', name: 'delete_facture', methods: ['POST'])]
public function deleteFacture(int $userId, int $factureId, Request $request, EntityManagerInterface $entityManager, FactureRepository $factureRepository): Response
{
    $facture = $factureRepository->find($factureId);
    if (!$facture) {
        throw $this->createNotFoundException('Facture non trouvée');
    }

    if ($this->isCsrfTokenValid('delete' . $facture->getId(), $request->request->get('_token'))) {
        $entityManager->remove($facture);
        $entityManager->flush();
    }

    return $this->redirectToRoute('user_show', ['id' => $userId]);
}

    #[Route('/admin/user/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonne $abonne, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbonneType::class, $abonne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/edit.html.twig', [
            'user' => $abonne,
            'form' => $form->createView(),
        ]);
    }
    //telecharger une facture 
    #[Route('/admin/facture/{id}/telecharger', name: 'download_facture')]
public function downloadFacture(Facture $facture): Response
{
    $filePath = $this->getParameter('facture_directory') . '/' . $facture->getFichier();

    // Afficher le chemin complet pour vérifier
    dump($filePath);

    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('Le fichier n\'existe pas.');
    }

    return $this->file($filePath, $facture->getFichier(), ResponseHeaderBag::DISPOSITION_INLINE);
}
//pour rajouter des nouveaux chantiers


      #[Route('/admin/chantier/new', name: 'admin_chantier_new', methods: ["GET", "POST"])]
     
    public function newChantier(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Vérifier que l'utilisateur est un administrateur
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Créer un nouveau chantier
        $chantier = new Chantier();

        // Créer le formulaire pour le chantier
        $form = $this->createForm(ChantierType::class, $chantier);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion des fichiers uploadés
            $images = $form->get('images')->getData();
            $uploadedImagePaths = [];

            if ($images) {
                foreach ($images as $image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                    // Déplace le fichier dans le répertoire où sont stockées les images
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );

                    // Sauvegarder le chemin de chaque image
                    $uploadedImagePaths[] = '/uploads/images/' . $newFilename;
                }

                // Enregistrer les chemins d'images dans la base de données
                $chantier->setImages($uploadedImagePaths);
            }

            // Sauvegarder le chantier dans la base de données
            $entityManager->persist($chantier);
            $entityManager->flush();

            return $this->redirectToRoute('app_realisations');
        }

        // Rendre la vue du formulaire dans l'administration
        return $this->render('admin/chantier_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
 //edit un chantier 
#[Route('/admin/chantier/{id}/edit_chantier', name: 'admin_chantier_edit')]
public function edit_chantier(Request $request, Chantier $chantier, EntityManagerInterface $em): Response
{
    $form = $this->createForm(ChantierType::class, $chantier);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        return $this->redirectToRoute('chantier_show', ['id' => $chantier->getId()]);
    }

    return $this->render('admin/chantier_edit.html.twig', [
        'form' => $form->createView(),
        'chantier' => $chantier,
    ]);
}


#[Route('/admin/dashboard/disponibilites', name: 'admin_disponibilites')]
public function index(EntityManagerInterface $em): Response
{
    $disponibilites = $em->getRepository(Disponibilite::class)->findAll();

    // Formater les disponibilités pour l'affichage
    $formattedDisponibilites = [];
    foreach ($disponibilites as $disponibilite) {
        $client = $disponibilite->getClient(); // Récupérer le client s'il existe
        $formattedDisponibilites[] = [
            'id' => $disponibilite->getId(),
            'date' => $disponibilite->getDate()->format('Y-m-d'),
            'heureDebut' => $disponibilite->getHeureDebut()->format('H:i'),
            'heureFin' => $disponibilite->getHeureFin()->format('H:i'),
            'disponible' => $disponibilite->isDisponible(),
            'client' => $client ? $client->getPrenom() : null, // Vérifiez si un client existe
        ];
    }

    return $this->render('admin/disponibilites.html.twig', [
        'disponibilites' => $formattedDisponibilites,
    ]);
}




#[Route('/admin/dashboard/disponibilites/new', name: 'admin_disponibilite_new')]
public function new(Request $request, EntityManagerInterface $em): Response
{
    $disponibilite = new Disponibilite();
    $form = $this->createForm(DisponibiliteType::class, $disponibilite);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($disponibilite);
        $em->flush();

        return $this->redirectToRoute('admin_disponibilites');
    }

    return $this->render('admin/new_disponibilite.html.twig', [
        'form' => $form->createView(),
    ]);
}


#[Route('/admin/dashboard/disponibilites/{id}/editrdv', name: 'admin_disponibilite_edit')]
public function editrdv(Disponibilite $disponibilite, Request $request, EntityManagerInterface $em): Response
{
    $form = $this->createForm(DisponibiliteType::class, $disponibilite);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();

        return $this->redirectToRoute('admin_disponibilites');
    }

    return $this->render('admin/edit_disponibilite.html.twig', [
        'form' => $form->createView(),
    ]);
}


#[Route('/admin/dashboard/disponibilites/{id}/deleterdv', name: 'admin_disponibilite_delete', methods: ['POST'])]
public function deleterdv(Disponibilite $disponibilite, EntityManagerInterface $em, Request $request): Response
{
    if ($this->isCsrfTokenValid('delete'.$disponibilite->getId(), $request->request->get('_token'))) {
        $em->remove($disponibilite);
        $em->flush();
    }

    return $this->redirectToRoute('admin_disponibilites');
}
}
