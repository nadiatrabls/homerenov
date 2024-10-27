<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/send-message', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request, MailerInterface $mailer): Response
    {
        // Récupérer les données du formulaire
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $message = $request->request->get('message');

        if ($name && $email && $message) {
            // Préparer et envoyer l'email
            $email = (new Email())
                ->from($email)
                ->to('contact@homerenov91.fr')
                ->subject('Nouveau message depuis le formulaire de contact')
                ->text("Nom: $name\nEmail: $email\nMessage: $message");

            $mailer->send($email);

            // Message de confirmation à l'utilisateur
            $this->addFlash('success', 'Votre message a été envoyé avec succès.');
        } else {
            // En cas d'erreur dans le formulaire
            $this->addFlash('error', 'Veuillez remplir tous les champs.');
        }

        // Redirection vers la page d'accueil
        return $this->redirectToRoute('app_home');
    }
}