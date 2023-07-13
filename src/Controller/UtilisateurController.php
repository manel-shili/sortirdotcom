<?php

namespace App\Controller;


use App\Form\ModifierProfilType;
use App\Repository\UtilisateurRepository;
use App\Service\ImageUpload;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    #[Route('/espace_membre/utilisateur', name: 'app_utlisateur')]
    public function index(
        UtilisateurRepository $utilisateurRepository
    ): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findBy(['isActif' => true]),
        ]);
    }

    #[Route('/espace_membre/profile', name: 'profile_edit')]
    public function edit(

        Request                     $request,
        EntityManagerInterface      $entityManager,
        UtilisateurRepository   $utilisateurRepository,
        ImageUpload $imageUpload
    ): Response
    {
        $user = $utilisateurRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $form = $this->createForm(ModifierProfilType::class , $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageUploaded = $form['nomPhoto']->getData();
            $newFileName = $imageUpload->upload($imageUploaded, $user->getId());
            $user-> setNomPhoto($newFileName);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('message' , 'Votre profil a été mis à jour.');
            return $this->redirect($this->generateUrl('profile_edit'));
        }
        return $this->render('utilisateur/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
