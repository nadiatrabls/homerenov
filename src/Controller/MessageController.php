<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MessageController extends AbstractController
{
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $message = $request->request->get('message');

            // Créer un email
            $emailMessage = (new Email())
                ->from($email)
                ->to('contact@homerenov91.fr') // Votre adresse
                ->subject('Nouveau message de contact')
                ->text("Nom : $name\nEmail : $email\nMessage : $message");

            // Envoyer l'email
            $mailer->send($emailMessage);

            // Rediriger ou afficher un message de succès
            return new Response('Votre message a été envoyé avec succès.');
        }

        return new Response('Formulaire de contact.');
    }
}
