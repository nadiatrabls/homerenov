<?php
namespace App\Controller;

use App\Repository\AbonneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid; // Utilisation correcte de Uuid pour générer des UUID
use Doctrine\ORM\EntityManagerInterface;

class PasswordResetController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_forgot_password')]
public function forgotPassword(Request $request, AbonneRepository $abonneRepository, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
{
    $error = null; // Initialiser la variable error à null

    if ($request->isMethod('POST')) {
        $email = $request->request->get('email');
        $user = $abonneRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $error = 'Utilisateur non trouvé.';
        } else {
            // Générer un token pour la réinitialisation
            $resetToken = Uuid::v4(); // Génère un UUID version 4
            $user->setResetToken($resetToken);
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoyer l'email de réinitialisation
            $emailMessage = (new Email())
                ->from('contact@homerenov91.fr')
                ->to($user->getEmail())
                ->subject('Réinitialisation de mot de passe')
                ->html($this->renderView('password_reset/emails.html.twig', [
                    'user' => $user,
                    'resetToken' => $resetToken,
                ]));

            $mailer->send($emailMessage);

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Un email vous a été envoyé pour réinitialiser votre mot de passe.');

            // Rediriger vers la même page ou une autre page
            return $this->redirectToRoute('app_forgot_password');
        }
    }

    return $this->render('password_reset/index.html.twig', [
        'error' => $error, // Toujours passer error dans le contexte
    ]);
}


    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(Request $request, AbonneRepository $abonneRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, $token): Response
    {
        $user = $abonneRepository->findOneBy(['resetToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Token invalide');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword)); // Utilisation du bon service pour hacher le mot de passe
            $user->setResetToken(null); // Supprimer le token après utilisation
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login', ['success' => 'Votre mot de passe a été réinitialisé avec succès.']);
        }

        return $this->render('password_reset/reset_password.html.twig', ['token' => $token]);
    }
}

