<?php

namespace App\Controller;

use App\Entity\RepresentantFamille;
use App\Form\RepresentantFamilleType;
use App\Repository\RepresentantFamilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/representant")
 */
class RepresentantFamilleController extends AbstractController
{
    /**
     * @Route("/", name="representant_famille_index", methods={"GET"})
     */
    public function index(RepresentantFamilleRepository $representantFamilleRepository): Response
    {
        return $this->render('representant_famille/index.html.twig', [
            'representant_familles' => $representantFamilleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/inscription", name="representant_famille_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $representantFamille = new RepresentantFamille();
        $form = $this->createForm(RepresentantFamilleType::class, $representantFamille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($representantFamille);
            $entityManager->flush();

            return $this->redirectToRoute('representant_famille_index');
        }

        return $this->render('representant_famille/new.html.twig', [
            'representant_famille' => $representantFamille,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="representant_famille_show", methods={"GET"})
     */
    public function show(RepresentantFamille $representantFamille): Response
    {
        return $this->render('representant_famille/show.html.twig', [
            'representant_famille' => $representantFamille,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="representant_famille_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RepresentantFamille $representantFamille): Response
    {
        $form = $this->createForm(RepresentantFamilleType::class, $representantFamille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('representant_famille_index');
        }

        return $this->render('representant_famille/edit.html.twig', [
            'representant_famille' => $representantFamille,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="representant_famille_delete", methods={"DELETE"})
     */
    public function delete(Request $request, RepresentantFamille $representantFamille): Response
    {
        if ($this->isCsrfTokenValid('delete'.$representantFamille->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($representantFamille);
            $entityManager->flush();
        }

        return $this->redirectToRoute('representant_famille_index');
    }
}
