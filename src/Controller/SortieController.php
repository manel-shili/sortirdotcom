<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Form\RechercheSortieType;
use App\Form\SortieType;
use App\Repository\InscriptionRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/espace_membre/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET', 'POST'])]
    public function index(Request $request, SortieRepository $sortieRepository, InscriptionRepository $inscriptionRepository): Response
    {

        $form = $this->createForm(RechercheSortieType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Lister les sorties demandées

            $choicesRequest = $form->getData();
            $choices['site'] = $choicesRequest['site'] ?? "";
            $choices['mot_cle'] = $choicesRequest['mot_cle'] ?? "";
            $choices['date_debut'] = $choicesRequest['date_debut'] ?? "";
            $choices['date_fin'] = $choicesRequest['date_fin'] ?? "";
            $choices['organisateur'] = $choicesRequest['organisateur'] ?? false;
            $choices['inscrit'] = $choicesRequest['inscrit'] ?? false;
            $choices['passe'] = $choicesRequest['passe'] ?? false;
            $choices['user_id'] = $this->getUser();
            $sorties = $sortieRepository->searchSortie($choices);
        } else {
            $sorties = $sortieRepository->findNotNull();
        }

        // Calcul de l'état de la sortie
        foreach ($sorties as $sortie){
            //Calcul du nombre d'inscrit
            $sortie->setNbInscrit($inscriptionRepository->count(['sortie'=>$sortie->getId()]));
            $sortie->setEstInscrit(false);

            //Utilisateur est-il inscrit ?
            $userId=$this->getUser()->getId();
            $sortieId=$sortie->getId();

            //Durée de la sortie
            $duree = date_diff($sortie->getDateFinSortie(), $sortie->getDateDebutSortie());
            $sortie->setDuree($duree);

            if($inscriptionRepository->estInscrit($userId,$sortieId)){
                $sortie->setEstInscrit(true);
            }

            //Determination des états
            $dateCourante= new \DateTime();
            $sortie->setEtat('');
            $sortie->calculEtat($dateCourante);

        }

        $SchemeAndHttpHost = $request->getSchemeAndHttpHost();

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView(),
            'SchemeAndHttpHost' => $SchemeAndHttpHost
        ]);
    }

    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SortieRepository $sortieRepository): Response
    {
        $sortie = new Sortie();
        $dateAujourdHui= new \DateTime();

        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {

            $sortie = $formSortie->getData();

            $sortie->setDateEnregistrement($dateAujourdHui);

            $datedebut = $formSortie['date_debut_sortie']->getData();
            $sortie->setDateDebutSortie($datedebut);

            $datefin = $formSortie['date_fin_sortie']->getData();
            $sortie->setDateFinSortie($datefin);

            if( $formSortie->get('Enregistrer')->isClicked()){
                $sortie->setDateEnregistrement($dateAujourdHui);
                //$sortie->setDateOuvertureInscription(NULL);
            }elseif( $formSortie->get('Publier')->isClicked()){
                $sortie->setDateOuvertureInscription($dateAujourdHui);

            }else{
                return $this->redirectToRoute('app_sortie_index');
            }

            $sortie->setOrganisateur($this->getUser());

            $this->addFlash('message', 'La sortie a été ajoutée.');

            $sortieRepository->save($sortie, true);
            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $formSortie,

        ]);
    }

    #[Route('/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(
        Sortie $sortie,
        InscriptionRepository $inscriptionRepository,
        UtilisateurRepository $utilisateurRepository
    ): Response
    {
        $dateCourante= new \DateTime();


        $sortie->calculEtat($dateCourante);
        //Durée de la sortie
        $duree = date_diff($sortie->getDateFinSortie(), $sortie->getDateDebutSortie());
        $sortie->setDuree($duree);

        $inscriptions = $inscriptionRepository->findBy(['sortie' => $sortie]);
        $inscrits = [];
        foreach ($inscriptions as $inscription) {
            $inscrits[] = $utilisateurRepository->findOneBy(['id' => $inscription->getUtilisateur()]);
        }
        $nb=count($inscrits) ;
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
            'inscrits' => $inscrits,
            'nbparticipant' => $nb,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dateAujourdHui= new \DateTime();
            if( $form->get('Enregistrer')->isClicked()){
                $sortie->setDateEnregistrement($dateAujourdHui);
                $sortie->setDateOuvertureInscription(NULL);
            }elseif( $form->get('Publier')->isClicked()){
                $sortie->setDateOuvertureInscription($dateAujourdHui);

            }else{
                return $this->redirectToRoute('app_sortie_index');
            }

            $sortieRepository->save($sortie, true);

            $this->addFlash('message', 'La sortie a bien été modifiée.');
            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toggleInscription', name: 'app_sortie_sinscrire', methods: ['POST'])]
    public function sinscrire(
        Request $request,
        Sortie $sortie,
        InscriptionRepository $inscriptionRepository,
        UtilisateurRepository $utilisateurRepository,
        EntityManagerInterface $entityManager
    )
    {
        $user = $this->getUser();
        $user = $utilisateurRepository->findOneBy(['username' => $user->getUserIdentifier()]);

        $nbInscrit = $inscriptionRepository->count(['sortie' => $sortie->getId()]);
        if (count($inscriptionRepository->estInscrit($user->getId(), $sortie->getId())) == 0) {
            if ($nbInscrit < $sortie->getNbInscriptionMax()) {
                $inscription = new Inscription();
                $inscription->setSortie($sortie);
                $inscription->setUtilisateur($user);
                $inscription->setDateInscription(new \DateTime("now"));
                $inscription->setIsParticipant(true);
                $entityManager->persist($inscription);
                $entityManager->flush();
                return new JsonResponse(['toggle' => true, 'direction' => "add", 'nbInscrit' => $nbInscrit + 1]);
            }
        } else {
            $inscription = $inscriptionRepository->findOneBy(['utilisateur' => $user->getId(), 'sortie' => $sortie->getId()]);
            $entityManager->remove($inscription);
            $entityManager->flush();
            return new JsonResponse(['toggle' => true, 'direction' => "remove", 'nbInscrit' => $nbInscrit - 1]);
        }

        return new JsonResponse(['toogle' => false, 'nbInscrit' => $nbInscrit]);
    }


}
