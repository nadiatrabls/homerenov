<?php

namespace App\Controller;

use App\Entity\Abonne; // Remplace par l'entité utilisée pour les abonnés
use App\Entity\Devis;
use App\Entity\Facture;
use App\Form\AbonneType; // Remplace par le type de formulaire pour les abonnés
use App\Form\DevisType;
use App\Form\FactureType;
use App\Repository\AbonneRepository;
use App\Repository\DevisRepository;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;



class AdminController extends AbstractController
{
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
#[Route('/admin/user/{id}/facture/upload', name: 'upload_facture', methods: ['GET', 'POST'])]
public function uploadFacture(Request $request, Abonne $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $facture = new Facture();
    $facture->setUser($user);

    $form = $this->createForm(FactureType::class, $facture);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gérer le fichier PDF de la facture
        /** @var UploadedFile $file */
        $file = $form->get('fichier')->getData();

        if ($file) {
            // Générer un nom de fichier unique
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

            $facture->setFichier($newFilename);
        }

        $entityManager->persist($facture);
        $entityManager->flush();

        return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
    }

    return $this->render('admin/show.html.twig', [
        'user' => $user,
        'form_facture' => $form->createView(),
        'form_devis' => $this->createForm(DevisType::class, new Devis())->createView(),
    ]);
}


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


// Routes pour voir, modifier et supprimer une facture
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

#[Route('/admin/user/{userId}/facture/{factureId}/edit', name: 'edit_facture', methods: ['GET', 'POST'])]
public function editFacture(Request $request, int $userId, int $factureId, EntityManagerInterface $entityManager, FactureRepository $factureRepository, SluggerInterface $slugger): Response
{
    $facture = $factureRepository->find($factureId);
    if (!$facture) {
        throw $this->createNotFoundException('Facture non trouvée');
    }

    $form = $this->createForm(FactureType::class, $facture);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $file = $form->get('fichier')->getData();
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($this->getParameter('facture_directory'), $newFilename);
            } catch (FileException $e) {
                return new Response('Erreur lors de l\'upload du fichier.');
            }
            $facture->setFichier($newFilename);
        }
        $entityManager->flush();
        return $this->redirectToRoute('user_show', ['id' => $userId]);
    }

    return $this->render('admin/edit_facture.html.twig', [
        'form' => $form->createView(),
        'facture' => $facture,
    ]);
}

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
 // Routes pour voir, modifier et supprimer un devis
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

 #[Route('/admin/user/{userId}/devis/{devisId}/edit', name: 'edit_devis', methods: ['GET', 'POST'])]
 public function editDevis(Request $request, int $userId, int $devisId, EntityManagerInterface $entityManager, DevisRepository $devisRepository, SluggerInterface $slugger): Response
 {
     $devis = $devisRepository->find($devisId);
     if (!$devis) {
         throw $this->createNotFoundException('Devis non trouvé');
     }

     $form = $this->createForm(DevisType::class, $devis);
     $form->handleRequest($request);

     if ($form->isSubmitted() && $form->isValid()) {
         $file = $form->get('fichier')->getData();
         if ($file) {
             $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
             $safeFilename = $slugger->slug($originalFilename);
             $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
             try {
                 $file->move($this->getParameter('devis_directory'), $newFilename);
             } catch (FileException $e) {
                 return new Response('Erreur lors de l\'upload du fichier.');
             }
             $devis->setFichier($newFilename);
         }
         $entityManager->flush();
         return $this->redirectToRoute('user_show', ['id' => $userId]);
     }

     return $this->render('admin/edit_devis.html.twig', [
         'form' => $form->createView(),
         'devis' => $devis,
     ]);
 }

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



}
