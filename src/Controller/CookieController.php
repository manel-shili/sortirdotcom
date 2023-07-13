<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CookieController extends AbstractController
{
    #[Route('/cookie', name: 'app_accept_cookie')]
    public function index(): Response

    {
        $response = new Response();
        $expires = time() + 36000;
        $cookie = Cookie::create('accept_cookie', 'true',  $expires);
        $response = $this->redirectToRoute('app_accueil');
        $response->headers->setCookie($cookie);
        $this->addFlash('message', 'Votre choix a été enregistré.');
        return $response;
    }
}
