<?php

// src/Controller/ContactController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactFormType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traitez ici l'envoi du formulaire
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    public function contact(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $messageContent = $request->request->get('message');

            // Créer l'email
            $emailMessage = (new Email())
                ->from($email)
                ->to('contact@homerenov91.fr') // Votre adresse email IONOS
                ->subject('Nouveau message de contact')
                ->text("Nom: $name\nEmail: $email\nMessage:\n$messageContent");

            // Envoyer l'email
            $mailer->send($emailMessage);

            // Optionnel : ajouter un message flash de confirmation
            $this->addFlash('success', 'Votre message a bien été envoyé.');

            return $this->redirectToRoute('contact_page');
        }

        return $this->render('home/index.html.twig');
    }
}
