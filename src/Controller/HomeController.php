<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Form\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use App\Repository\ChantierRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, MailerInterface $mailer, ChantierRepository $chantierRepository): Response
    public function index(Request $request, MailerInterface $mailer, ChantierRepository $chantierRepository): Response
    {
        $chantiersSpecifiques = $chantierRepository->findBy(['id' => [5, 8, 14]]);
        $chantiers = $chantierRepository->findBy([], null, 8);

        // Créer le formulaire de contact
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        // Vérifiez si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();

            // Créer l'email
            $email = (new TemplatedEmail())
                ->from(new Address('contact@homerenov91.fr', 'Homerenov91'))
                ->to('homerenovations91@gmail.com')
                ->subject('Nouvelle demande de contact')
                ->htmlTemplate('emails/contact_message.html.twig')
                ->context([
                    'name' => $data['name'],
                    'contactEmail' => $data['email'], // Renommé ici
                    'message' => $data['message'],
                ]);

            // Envoyer l'email
            $mailer->send($email);

            // Ajouter un message flash de confirmation
            $this->addFlash('success', 'Votre message a été envoyé avec succès !');

            // Rediriger pour éviter la resoumission du formulaire
            return $this->redirectToRoute('app_home');
        }

        // Créer le formulaire de contact
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        // Vérifiez si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();

            // Créer l'email
            $email = (new TemplatedEmail())
                ->from(new Address('contact@homerenov91.fr', 'Homerenov91'))
                ->to('homerenovations91@gmail.com')
                ->subject('Nouvelle demande de contact')
                ->htmlTemplate('emails/contact_message.html.twig')
                ->context([
                    'name' => $data['name'],
                    'contactEmail' => $data['email'], // Renommé ici
                    'message' => $data['message'],
                ]);

            // Envoyer l'email
            $mailer->send($email);

            // Ajouter un message flash de confirmation
            $this->addFlash('success', 'Votre message a été envoyé avec succès !');

            // Rediriger pour éviter la resoumission du formulaire
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'chantiers' => $chantiers,
            'chantiersSpecifiques' => $chantiersSpecifiques,
            'form' => $form->createView(),
            'form' => $form->createView(),
        ]);

        
    }

    

}
