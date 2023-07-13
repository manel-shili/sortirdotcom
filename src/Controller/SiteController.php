<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\RechercheSiteType;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/admin/site')]
class SiteController extends AbstractController
{
    #[Route('/', name: 'app_site_index', methods: ['GET' , 'POST'])]
    public function index(Request $request ,SiteRepository $siteRepository): Response
    {
        $site = new Site();
        $form = $this->createForm(RechercheSiteType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $key = $request->request->all()['recherche_site']['nom'];
            $sites = $siteRepository->findByNom($key);
        }
        else {
            $sites = $siteRepository->findAll();
        }
        return $this->render('site/index.html.twig', [
            'sites' => $sites,
            'form' =>$form->createView()
        ]);
    }

    #[Route('/new', name: 'app_site_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SiteRepository $siteRepository): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (count($siteRepository->findBy(['localisation' => $site->getLocalisation()])) == 0)  {
                $siteRepository->save($site, true);

                $this->addFlash('message', 'Le site a été ajouté.');
                return $this->redirectToRoute('app_site_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('message', 'Ce lieu est déjà associé à un site.');
            }
        }

        return $this->renderForm('site/new.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_site_show', methods: ['GET'])]
    public function show(Site $site): Response
    {
        return $this->render('site/show.html.twig', [
            'site' => $site,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_site_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Site $site, SiteRepository $siteRepository): Response
    {
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $siteRepository->save($site, true);

            $this->addFlash('message', 'La site a bien été ajouté.');
            return $this->redirectToRoute('app_site_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('site/edit.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_site_delete', methods: ['POST'])]
    public function delete(Request $request, Site $site, SiteRepository $siteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$site->getId(), $request->request->get('_token'))) {
            $siteRepository->remove($site, true);
        }

        return $this->redirectToRoute('app_site_index', [], Response::HTTP_SEE_OTHER);
    }
}
