<?php

namespace App\Controller;

use App\Entity\Dispositions;
use App\Entity\Droits;
use App\Entity\Gestionnaires;
use App\Entity\RepresentantFamille;
use App\Form\GestionnaireType;
use App\Repository\RepresentantFamilleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\DateTime;
use Twig\Environment;
use App\Entity\InformationsFamille;
use App\Entity\MembreFamille;
use App\Form\RepresentantFamilleType;
use App\Form\InformationFamilleType;

/**
 * @Route("/gestionnaire/superAdmin")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class SuperAdminController extends AbstractController
{
    /**
     * @Route("/listeGestionnaires", name="SuperAdmin.ListeGestionnaires.show", methods={"GET"})
     */
    public function showListeGestionnaires(Environment $twig, RegistryInterface $doctrine, Request $request)
    {
        $gestionnaires = $doctrine->getRepository(Gestionnaires::class)->findAll();
        return new Response($twig->render('back_office/super_admin/listeGestionnaires.html.twig', [
            'gestionnaires' => $gestionnaires,
            'connected_gestionnaire' => $this->getUser(),
        ]));
    }

    /**
     * @Route("/gestionnaire/{id}", name="SuperAdmin.Gestionnaire.show")
     */
    public function showGestionnaire(Environment $twig, RegistryInterface $doctrine, Gestionnaires $gestionnaire) : Response
    {
        $showGestionnaire = $doctrine->getRepository(Gestionnaires::class)->find($gestionnaire->getId());
        return new Response($twig->render('back_office/super_admin/showGestionnaire.html.twig', [
            'selected_gestionnaire' => $showGestionnaire,
            'connected_gestionnaire' => $this->getUser(),
        ]));
    }

    /**
     * @Route("/ajouterGestionnaire", name="SuperAdmin.Gestionnaire.add")
     */
    public function addGestionnaire(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $gestionnaire = new Gestionnaires();
        $form = $this->createForm(GestionnaireType::class, $gestionnaire);
        $form->handleRequest($request);
        $passwordsMatch = strcmp($form->get("motDePasse")->getData(),$form->get("confimerMDP")->getData())==0;

        if(!$passwordsMatch)
        {
            $form->get('confimerMDP')->addError(new FormError('Les deux mots de passes ne correspondent pas'));
        }

        if ($form->isSubmitted() && $form->isValid()&& $passwordsMatch)
        {
            $hash = $encoder->encodePassword($gestionnaire, $gestionnaire->getMotdepasse());
            $gestionnaire->setMotdepasse($hash);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($gestionnaire);
            $entityManager->flush();
            $this->addFlash('succes', 'Gestionnaire ajouté !');

            return $this->redirectToRoute('SuperAdmin.ListeGestionnaires.show');
        }

        return $this->render('back_office/super_admin/addGestionnaire.html.twig', [
            'form' => $form->createView(),
            'connected_gestionnaire' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/gestionnaire/{id}/modifierGestionnaire", name="SuperAdmin.Gestionnaire.edit")
     */
    public function editGestionnaire(Request $request, Gestionnaires $gestionnaire, UserPasswordEncoderInterface $encoder) : Response
    {
        $form = $this->createForm(GestionnaireType::class, $gestionnaire);
        $form->handleRequest($request);
        $passwordsMatch = strcmp($form->get("motDePasse")->getData(),$form->get("confimerMDP")->getData())==0;

        if(!$passwordsMatch)
        {
            $form->get('confimerMDP')->addError(new FormError('Les deux mots de passes ne correspondent pas'));
        }

        if ($form->isSubmitted() && $form->isValid()&& $passwordsMatch)
        {
            $hash = $encoder->encodePassword($gestionnaire, $gestionnaire->getMotdepasse());
            $gestionnaire->setMotdepasse($hash);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('succes', 'Gestionnaire modifié !');

            return $this->redirectToRoute('SuperAdmin.ListeGestionnaires.show');
        }

        return $this->render('back_office/super_admin/editGestionnaire.html.twig', [
            'selected_gestionnaire' => $gestionnaire,
            'form' => $form->createView(),
            'connected_gestionnaire' => $this->getUser(),
        ]);
    }

    // Pourquoi cette fonction ? Pour vérifier si c'est le SUPER_ADMIN (il ne peut pas se supprimer)
    public function isSuperAdmin($dispositions)
    {
        for ( $i=0; $i<count($dispositions); $i++ )
        {
            if ( $dispositions[$i]->getDroits()->getCode() == 'DROITS_SUPER_ADMIN' ) { return true; }
        }
        return false;
    }

    /**
     * @Route("/gestionnaire/{id}/supprimerGestionnaire", name="SuperAdmin.Gestionnaire.delete")
     */
    public function deleteGestionnaire(RegistryInterface $doctrine, Request $request, Gestionnaires $gestionnaire) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $gestionnaire]);
        if ( !$this->isSuperAdmin($dispositions) )
        {
            try {
                for ( $i=0; $i<count($dispositions); $i++ )
                {
                    $doctrine->getEntityManager()->remove($dispositions[$i]);
                }
                $doctrine->getEntityManager()->remove($gestionnaire);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Gestionnaire supprimé !');

            return $this->redirectToRoute('SuperAdmin.ListeGestionnaires.show');
        }
        return $this->render('erreurs/superAdmin_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'erreur' => "supprimer votre propre compte",
        ]);
    }
}


