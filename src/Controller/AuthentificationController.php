<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Utilise `Annotation\Route` au lieu de `Attribute\Route`
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthentificationController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, on vérifie son rôle et on redirige en conséquence
        if ($this->getUser()) {
            // Vérifier si l'utilisateur a le rôle d'admin
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_dashboard'); // Redirige vers le tableau de bord admin
            }

            // Si l'utilisateur est un user normal
            if ($this->isGranted('ROLE_USER')) {
                return $this->redirectToRoute('user_dashboard', ['id' => $this->getUser()->getId()]); // Redirige vers le tableau de bord utilisateur
            }
        }

        // Récupérer l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Dernier nom d'utilisateur entré par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercept by the logout key on your firewall.');
    }
}
