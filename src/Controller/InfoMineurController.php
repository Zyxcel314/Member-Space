<?php
/**
 * Created by PhpStorm.
 * User: zalaahaz
 * Date: 18/12/19
 * Time: 16:28
 */

namespace App\Controller;

use App\Entity\InformationMajeur;
use App\Entity\InformationResponsableLegal;
use App\Entity\InformationsMineur;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
use App\Form\InfoMineurType;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Bridge\Doctrine\RegistryInterface;   // ORM Doctrine
use Symfony\Component\HttpFoundation\Request;    // objet REQUEST
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class InfoMineurController extends AbstractController
{

    /**
     * @Route("/showInfoMineur/{n}", name="Membre.showMineur", methods={"GET"})
     */
    public function showInfoMineur(Environment $twig, RegistryInterface $doctrine, Request $request, $n)
    {
        $mineur = $doctrine->getRepository(InformationsMineur::class)->findAll();
        return new Response($twig->render('membre_famille/mineur/showInfoMineur.html.twig', ['mineur' => $mineur]));
    }

    /**
     * @Route("/ajouterInfoMineur/{n}", name="Membre.addInfoMineur")
     */
    public function addInfoMineur(Request $request, Environment $twig, RegistryInterface $doctrine, FormFactoryInterface $formFactory, $n)
    {
        $form=$formFactory->createBuilder(InfoMineurType::class)->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineurs=$form->getData();
            $mineurs->setMembreFamille($doctrine->getRepository(MembreFamille::class)->find($n));
            $doctrine->getEntityManager()->persist($mineurs);
            $doctrine->getEntityManager()->flush();
            return $this->redirectToRoute('Membre.showMembres');

            $this->addFlash('success', 'Informations ajoutées');
        }
        return new Response($twig->render('membre_famille/mineur/addInfoMineur.html.twig',['form'=>$form->createView()]));
    }

    /**
     * @Route("/editerInfoMineur/{n}", name="Membre.editInfoMineur")
     */
    public function editInfoMineur(Request $request, Environment $twig, RegistryInterface $doctrine, FormFactoryInterface $formFactory, $n)
    {
        $coordonnees=$doctrine->getRepository(InformationsMineur::class)->find($n);
        $form=$formFactory->createBuilder(InfoMineurType::class,$coordonnees)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getEntityManager()->flush();
            return $this->redirectToRoute('Membre.showMineur');

            $this->addFlash('success', 'Informations mineurs mises à jour');

            return $this->redirectToRoute('Membre.showMineur');
        }
        return $this->render('membre_famille/mineur/editInfoMineur.html.twig',['form'=>$form->createView()]);
    }
}