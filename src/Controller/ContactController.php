<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            // Récupération des données du formulaire
            $projectType = $request->get('projectType', []);
            $localType = $request->get('localType', []);
            $surface = $request->get('surface');
            $budget = $request->get('budget');
            $location = $request->get('location');
            $firstName = $request->get('firstName');
            $lastName = $request->get('lastName');
            $phone = $request->get('phone');
            $contactEmail = $request->get('email');
            $projectDetails = $request->get('projectDetails');

            // Convertir les tableaux en chaînes de caractères
            $projectTypeString = implode(', ', $projectType);
            $localTypeString = implode(', ', $localType);

            // Configuration de l'email
            $emailMessage = (new TemplatedEmail())
                ->from(new Address('contact@homerenov91.fr', 'Homerenov 91'))
                ->to('homerenovations91@gmail.com')
                ->subject('Nouvelle demande d\'étude')
                ->htmlTemplate('emails/study_request.html.twig')
                ->context([
                    'projectType' => $projectTypeString,
                    'localType' => $localTypeString,
                    'surface' => $surface,
                    'budget' => $budget,
                    'location' => $location,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'phone' => $phone,
                    'contactEmail' => $contactEmail,
                    'projectDetails' => $projectDetails,
                ]);

            // Envoi de l'email
            $mailer->send($emailMessage);

            // Redirection avec message de confirmation
            $this->addFlash('success', 'Votre demande d\'étude a été envoyée avec succès !');
            return $this->redirectToRoute('contact');
        }

        // Affichez la page de demande d'étude (formulaire)
        return $this->render('contact/index.html.twig');
    }
}