<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Form\MessageformType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

class MessagesController extends AbstractController
{
    #[Route('/messages', name: 'app_messages')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(MessageformType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Send the email
            $mail = (new TemplatedEmail())
            ->from(new Address('contact@homerenov91.fr', 'Web Dev Contact')) // IONOS email in the 'from' field
            ->to('kolonelaboki78@gmail.com')
            ->subject('Contact Form Submission')
            ->htmlTemplate('emails/index.html.twig')
            ->context(['data' => $data]);

        $mailer->send($mail);

        $this->addFlash('success', 'Your message has been sent and saved successfully!');
        return $this->redirectToRoute('app_home');
    }

        return $this->render('home/formulaire.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
