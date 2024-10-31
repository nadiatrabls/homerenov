<?php

namespace App\Controller;
use App\Entity\ContactMessage;
use App\Form\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\ChantierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ChantierRepository $chantierRepository): Response
    {
        $chantiersSpecifiques = $chantierRepository->findBy(['id' => [5, 8, 14]]);
        $chantiers = $chantierRepository->findBy([], null, 8);

        return $this->render('home/index.html.twig', [
            'chantiers' => $chantiers,
            'chantiersSpecifiques' => $chantiersSpecifiques,
        ]);

        
    }

    

}
