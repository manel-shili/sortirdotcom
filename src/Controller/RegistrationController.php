<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Form\RegistrationUtilisateurFormType;
use App\Repository\UtilisateurRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/admin/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Default values
            $user->setIsCguAccepte(false);
            $user->setIsActif(false);
            // encode the plain password
            //By default provisional password is generated randomly
            try {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        random_bytes(15)
                    )
                );
            } catch (\Exception $e) {
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@sortirdotcom.com', 'SortirDotCom'))
                    ->to($user->getCourriel())
                    ->subject('Confirmation courriel')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            $newUser = new Utilisateur();
            $newForm = $this->createForm(RegistrationFormType::class, $newUser);
            return $this->render('registration/register.html.twig', [
                'registrationForm' => $newForm->createView(),
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        // Forbidden for connected users
        if(null !== $this->getUser()) {
            $this->addFlash('message', 'Vous auriez dû être déconnecté pour accéder à cette page.');
            return $this->redirectToRoute('app_accueil');
        }

        // retrieve the user id from the url
        $id = $request->get('id');

        // Verify the user id exists and is not null
        if (null === $id) {
            return $this->redirectToRoute('app_accueil');
        }

        $user = $utilisateurRepository->find($id);

        // Ensure the user exists in persistence
        if (null === $user) {
            return $this->redirectToRoute('app_accueil');
        }

        // Ensure the user has not already submit this form
        if ($user->isIsCguAccepte()) {
            $this->addFlash('message', 'Vous n\'avez pas l\'autorisation d\accéder à cette page.');
            return $this->redirectToRoute('app_accueil');
        }

        // Validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('message', $exception->getReason());
            return $this->redirectToRoute('app_register_utilisateur_expire');
        }
        $this->addFlash('message', 'Votre courriel a bien été vérifié.');

        // Pass safely id_nouveau_utilisateur to app_register_utilisateur route method
        $request->getSession()->set("id_nouveau_utilisateur", $user->getId());

        // Go to app_register_utilisateur
        $form = $this->createForm(RegistrationUtilisateurFormType::class, $user);

        return $this->render('registration/register_utilisateur.html.twig', [
            'registrationUtilisateurForm' => $form->createView()
        ]);
    }

    #[Route('/register', name: 'app_register_utilisateur')]
    public function registerUtilisateur(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UtilisateurRepository $utilisateurRepository
    ): Response
    {
        // Forbidden for connected users
        if(null !== $this->getUser()) {
            $this->addFlash('message', 'Vous auriez dû être déconnecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');
        }

        $user_id = $request->getSession()->get('id_nouveau_utilisateur');

        $users = $utilisateurRepository->findBy(['id' => $user_id]);
        // Ensure the user has not already submit this form
        if (empty($users) || $users[0]->isIsCguAccepte()) {
            $this->addFlash('message', 'Vous n\'avez pas l\'autorisation d\accéder à cette page.');
            return $this->redirectToRoute('app_accueil');
        }
        $user = $users[0];

        $form = $this->createForm(RegistrationUtilisateurFormType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // Set actif to true by defautl
            $user->setIsActif(true);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('message', 'Votre inscription est finalisée.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register_utilisateur.html.twig', [
            'registrationUtilisateurForm' => $form->createView()
        ]);
    }

    #[Route('/register', name: 'app_register_utilisateur_expire')]
    public function registerUtilisateurExpire(): Response {
        return $this->render('registration/register_utilisateur_expire.html.twig');
    }
}
