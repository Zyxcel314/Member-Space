<?php

namespace App\Controller;

use App\Entity\Dispositions;
use App\Entity\Droits;
use App\Entity\Gestionnaires;
use App\Form\DispositionsType;
use App\Form\DroitsType;
use App\Repository\RepresentantFamilleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * @Route("/gestionnaire")
 * @IsGranted("ROLE_ADMIN")
 */
class DroitsController extends AbstractController
{
    /**
     * @Route("/listeDroits", name="Droits.ListeDroits.show")
     */
    public function showListeDroits(Environment $twig, RegistryInterface $doctrine)
    {
        $droits = $doctrine->getRepository(Droits::class)->findAll();
        return new Response($twig->render('back_office/droits/showDroitsPerso.html.twig', [
            'droits' => $droits,
            'connected_gestionnaire' => $this->getUser(),
        ]));
    }

    /**
     * @Route("/droits", name="Gestionnaire.DroitsPerso.show")
     */
    public function showDroitsPerso(Environment $twig, RegistryInterface $doctrine)
    {
        $gestionnaire = $doctrine->getRepository(Gestionnaires::class)->find($this->getUser()->getId());
        $dispositions = $doctrine->getRepository(Dispositions::class)->findOneBy(array('gestionnaire' => $gestionnaire));
        $dispositionsArray = $doctrine->getRepository(Dispositions::class)->findBy(array('gestionnaire' => $gestionnaire));

        return new Response($twig->render('back_office/droits/showDroitsPerso.html.twig', [
            'dispositions' => $dispositions,
            'dispositionsArray' => $dispositionsArray,
            'connected_gestionnaire' => $this->getUser(),
        ]));
    }


    /**
     * @Route("/superAdmin/gestionnaire/{id}/droits", name="SuperAdmin.Droits.show")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function showDroits(Environment $twig, RegistryInterface $doctrine, Gestionnaires $gestionnaire) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findOneBy(array('gestionnaire' => $gestionnaire));
        $dispositionsArray = $doctrine->getRepository(Dispositions::class)->findBy(array('gestionnaire' => $gestionnaire));

        return new Response($twig->render('back_office/super_admin/showDroits.html.twig', [
            'dispositions' => $dispositions,
            'dispositionsArray' => $dispositionsArray,
            'selected_gestionnaire' => $gestionnaire,
            'connected_gestionnaire' => $this->getUser(),
        ]));
    }

    // Pourquoi cette fonction ? Pour vérifier si les droits appartiennent au SUPER_ADMIN (il ne peut pas s'en ajouter ou se les modifier)
    public function isSuperAdmin($dispositions)
    {
        for ( $i=0; $i<count($dispositions); $i++ )
        {
            if ( $dispositions[$i]->getDroits()->getCode() == 'DROITS_SUPER_ADMIN' ) { return true; }
        }
        return false;
    }

    /**
     * @Route("/superAdmin/gestionnaire/{id}/ajouterDroits", name="SuperAdmin.Droits.add")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function addDroitsPerso(RegistryInterface $doctrine, Request $request, Gestionnaires $gestionnaire) : Response
    {
        $dispositionsSuperAdmin = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $gestionnaire]);
        $allDroits = $doctrine->getRepository(Droits::class)->findAll();
        $retourForm = $request->get('envoiForm');
        if ( !$this->isSuperAdmin($dispositionsSuperAdmin) )
        {
            if ( !is_null($retourForm) )
            {
                $selected_droits = $request->get("selected_droits");
                for ( $i=0; $i<count($selected_droits); $i++ )
                {
                    for ( $j=0; $j<count($allDroits); $j++ )
                    {
                        if ($selected_droits[$i] == $allDroits[$j]->getCode())
                        {
                            $dispositions = new Dispositions();
                            $dispositions
                                ->setGestionnaire($gestionnaire)
                                ->setDroits($allDroits[$j]);
                            $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->persist($dispositions);
                            $entityManager->flush();
                        }
                    }
                }
                $this->addFlash('succes', 'Droits ajoutés !');
                return $this->redirectToRoute('SuperAdmin.Droits.show', ['id' => $gestionnaire->getId()]);
            }
            return $this->render('back_office/super_admin/addDroits.html.twig', [
                'selected_gestionnaire' => $gestionnaire,
                'connected_gestionnaire' => $this->getUser(),
            ]);
        }
        return $this->render('erreurs/superAdmin_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'erreur' => "vous ajouter des droits",
        ]);
    }

    /**
     * @Route("/superAdmin/gestionnaire/{id}/modifierDroits", name="SuperAdmin.Droits.edit")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function editDroitsPerso(Request $request, Environment $twig, RegistryInterface $doctrine, Gestionnaires $gestionnaire) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $gestionnaire]);
        $allDroits = $doctrine->getRepository(Droits::class)->findAll();
        $retourForm = $request->get('envoiForm');
        if (!$this->isSuperAdmin($dispositions))
        {
            if ( !is_null($retourForm) )
            {
                // On supprime d'abord les anciens droits...
                for ( $i=0; $i<count($dispositions); $i++ )
                {
                    try {
                        $doctrine->getEntityManager()->remove($dispositions[$i]);
                    } catch (ORMException $e) {
                    }
                }
                // ...puis on ajoute les nouveaux sélectionnés
                $selected_droits = $request->get("selected_droits");
                for ( $i=0; $i<count($selected_droits); $i++ )
                {
                    for ( $j=0; $j<count($allDroits); $j++ )
                    {
                        if ($selected_droits[$i] == $allDroits[$j]->getCode())
                        {
                            var_dump($selected_droits[$i]);
                            var_dump($allDroits[$j]->getCode());
                            $dispositions = new Dispositions();
                            $dispositions
                                ->setGestionnaire($gestionnaire)
                                ->setDroits($allDroits[$j]);
                            $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->persist($dispositions);
                            $entityManager->flush();
                        }
                    }
                }
                $this->addFlash('succes', 'Droits ajoutés !');
                return $this->redirectToRoute('SuperAdmin.Droits.show', ['id' => $gestionnaire->getId()]);
            }
            return $this->render('back_office/super_admin/editDroits.html.twig', [
                'dispositions_gestionnaire' => $dispositions,
                'selected_gestionnaire' => $gestionnaire,
                'connected_gestionnaire' => $this->getUser(),
            ]);
        }
        return $this->render('erreurs/superAdmin_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'erreur' => "vous ajouter des droits",
        ]);
    }
}

/* Vieux code NON fonctionnel avec le form de Symfony
            $form = $this->createForm(DispositionsType::class, $dispositions);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $dispositions
                    ->setGestionnaire($gestionnaire);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($dispositions);
                $entityManager->flush();
                $this->addFlash('succes', 'Droits ajoutés !');

                return $this->redirectToRoute('SuperAdmin.Droits.show', ['id' => $gestionnaire->getId()]);
            }

            return $this->render('back_office/super_admin/addDroits.html.twig', [
                'dispositions' => $dispositions,
                'selected_gestionnaire' => $gestionnaire,
                'form' => $form->createView(),
                'connected_gestionnaire' => $this->getUser(),
            ]);
*/
