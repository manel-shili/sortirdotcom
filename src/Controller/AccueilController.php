<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        $nbActif = $utilisateurRepository->count(['isActif' => true]);

        return $this->render('accueil/index.html.twig', [
            'last_username' => "", // see login
            'error' => "", // see login
            'nbActif' => $nbActif
        ]);
    }
}
