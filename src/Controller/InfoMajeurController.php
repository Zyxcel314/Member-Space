<?php
/**
 * Created by PhpStorm.
 * User: zalaahaz
 * Date: 19/12/19
 * Time: 08:27
 */

namespace App\Controller;

use App\Entity\InformationMajeur;
use App\Entity\InformationResponsableLegal;
use App\Entity\InformationsMineur;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
use App\Form\InfoMajeurType;
use App\Form\InfoMineurType;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\AbstractType;
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

class InfoMajeurController extends AbstractController
{

    /**
     * @Route("/showInfoMajeur", name="Membre.showMajeur", methods={"GET"})
     */
    public function showInfoMajeur(Environment $twig, RegistryInterface $doctrine)
    {
        $majeur = $doctrine->getRepository(InformationMajeur::class)->findAll();
        return new Response($twig->render('membre_famille/majeur/showInfoMajeur.html.twig', ['majeur' => $majeur]));
    }

    /**
     * @Route("/ajouterInfoMajeur/{n}", name="Membre.addInfoMajeur")
     */
    public function addInfoMajeur(Request $request, Environment $twig, RegistryInterface $doctrine, FormFactoryInterface $formFactory, $n)
    {
        $form=$formFactory->createBuilder(InfoMajeurType::class)->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $majeurs=$form->getData();
            $majeurs->setMembreFamille($doctrine->getRepository(MembreFamille::class)->find($n));
            $doctrine->getEntityManager()->persist($majeurs);
            $doctrine->getEntityManager()->flush();
            return $this->redirectToRoute('Membre.showMembres');

            $this->addFlash('success', 'Informations ajoutées');
        }
        return new Response($twig->render('membre_famille/majeur/addInfoMajeur.html.twig',['form'=>$form->createView()]));
    }

    /**
     * @Route("/editerInfoMajeur/{n}", name="Membre.editInfoMajeur")
     */
    public function editInfoMajeur(Request $request, Environment $twig, RegistryInterface $doctrine, FormFactoryInterface $formFactory, $n)
    {
        $coordonnees=$doctrine->getRepository(InformationMajeur::class)->find($n);
        $form=$formFactory->createBuilder(InfoMajeurType::class,$coordonnees)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getEntityManager()->flush();
            return $this->redirectToRoute('Membre.showMajeur');

            $this->addFlash('success', 'Informations mineurs mises à jour');
        }
        return new Response($twig->render('membre_famille/majeur/editInfoMajeur.html.twig',['form'=>$form->createView()]));
    }
}