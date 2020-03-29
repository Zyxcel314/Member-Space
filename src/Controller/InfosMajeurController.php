<?php

namespace App\Controller;

use App\Entity\Dispositions;
use App\Entity\Droits;
use App\Entity\InformationMajeur;
use App\Entity\InformationResponsableLegal;
use App\Entity\InformationsMineur;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
use App\Form\InfosMajeurType;
use App\Form\InfosMineurType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
 * @IsGranted("ROLE_USER")
 */
class InfosMajeurController extends AbstractController
{
    public function hasRepresentantDroits(RegistryInterface $doctrine, $representant, $membre)
    {
        $membresRepresentant = $doctrine->getRepository(MembreFamille::class)->findBy(['representant_famille' => $representant->getId()], ['id' => 'ASC']);
        foreach ( $membresRepresentant as $membreRepresentant )
        {
            if ( $membreRepresentant->getId() == $membre->getId() )
            {
                return true;
            }
        }
        return false;
    }

    /**
     * @Route("/membre/{id}/informationsMajeur", name="InfosMajeur.show")
     */
    public function showInfosMajeurUSER(Request $request, Environment $twig, RegistryInterface $doctrine, MembreFamille $membreFamille) : Response
    {
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        if ( $this->hasRepresentantDroits($doctrine, $representant, $membreFamille) )
        {
            $infosMajeur = $doctrine->getRepository(InformationMajeur::class)->findOneBy(array('membre_famille' => $membreFamille));
            return new Response($twig->render('front_office/membre_famille/majeur/showInfosMajeur.html.twig', [
                'membre' => $membreFamille,
                'representant' => $this->getUser()->getId(),
                'infosMajeur' => $infosMajeur,
            ]));
        }
        else
        {
            return $this->render('erreurs/representant_noDroits.html.twig', [
                'representant' => $representant,
                'membre' => $membreFamille,
                'nomOperation' => "voir"
            ]);
        }
    }

    /**
     * @Route("/membre/{id}/ajouterInfosMajeur", name="InfosMajeur.add")
     */
    public function addInfosMajeurUSER(RegistryInterface $doctrine, Request $request, MembreFamille $membreFamille) : Response
    {
        $infosMajeur = new InformationMajeur();
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        if ( $this->hasRepresentantDroits($doctrine, $representant, $membreFamille) )
        {
            $form = $this->createForm(InfosMajeurType::class, $infosMajeur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $infosMajeur
                    ->setMembreFamille($membreFamille);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosMajeur);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations du majeur ajoutées !');

                return $this->redirectToRoute('InfosMajeur.show', ['id' => $membreFamille->getId()]);
            }

            return $this->render('front_office/membre_famille/majeur/addInfosMajeur.html.twig', [
                'infosMajeur' => $infosMajeur,
                'membre' => $membreFamille,
                'representant' => $this->getUser()->getId(),
                'form' => $form->createView(),
            ]);
        }
        else
        {
            return $this->render('erreurs/representant_noDroits.html.twig', [
                'representant' => $representant,
                'membre' => $membreFamille,
                'nomOperation' => "ajouter"
            ]);
        }
    }

    /**
     * @Route("/membre/{id}/modifierInfosMajeur", name="InfosMajeur.edit")
     */
    public function editInfosMajeurUSER(Request $request, RegistryInterface $doctrine, MembreFamille $membreFamille) : Response
    {
        $infosMajeur = $doctrine->getRepository(InformationMajeur::class)->findOneBy(array('membre_famille' => $membreFamille));
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        if ( $this->hasRepresentantDroits($doctrine, $representant, $membreFamille) )
        {
            $form = $this->createForm(InfosMajeurType::class, $infosMajeur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosMajeur);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations du majeur modifiées !');

                return $this->redirectToRoute('InfosMajeur.show', ['id' => $membreFamille->getId()]);
            }

            return $this->render('front_office/membre_famille/majeur/editInfosMajeur.html.twig', [
                'infosMajeur' => $infosMajeur,
                'membre' => $membreFamille,
                'representant' => $this->getUser()->getId(),
                'form' => $form->createView(),
            ]);
        }
        else
        {
            return $this->render('erreurs/representant_noDroits.html.twig', [
            'representant' => $representant,
            'membre' => $membreFamille,
            'nomOperation' => "modifier"
            ]);
        }
    }

    /**
     * @Route("/membre/{id}/supprimerInfosMajeur", name="InfosMajeur.delete")
     */
    public function deleteInfosMajeurUSER(Request $request, RegistryInterface $doctrine, MembreFamille $membreFamille)
    {
        $infosMajeur = $doctrine->getRepository(InformationMajeur::class)->findOneBy(array('membre_famille' => $membreFamille));
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        if ( $this->hasRepresentantDroits($doctrine, $representant, $membreFamille) )
        {
            try {
                if ( $infosMajeur != null )
                {
                    $infosMajeur->setMembreFamille(null);
                }
                $doctrine->getEntityManager()->remove($infosMajeur);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Informations du majeur supprimées !');

            return $this->redirectToRoute('InfosMajeur.show', ['id' => $membreFamille->getId()]);
        }
        else
        {
            return $this->render('erreurs/representant_noDroits.html.twig', [
                'representant' => $representant,
                'membre' => $membreFamille,
                'nomOperation' => "modifier"
            ]);
        }
    }


    public function droitNecessaire($listeDroits, String $codeDroit)
    {
        for ( $i=0; $i<count($listeDroits); $i++ )
        {
            if ( $listeDroits[$i]->getCode() == $codeDroit ) { return $listeDroits[$i]->getLibelle(); }
        }
        return "ERREUR : Fonction droitNecessaire()";
    }

    public function hasDroits($dispositions, String $codeDroit)
    {
        for ( $i=0; $i<count($dispositions); $i++ )
        {
            if ( $dispositions[$i]->getDroits()->getCode() == $codeDroit ) { return true; }
        }
        return false;
    }

    /**
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/informationsMajeur", name="Gestionnaire.InfosMajeur.show")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showInfosMajeurGEST(Request $request, Environment $twig, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_VOIR");

        if ( $this->hasDroits($dispositions, "INFOS_VOIR") )
        {
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $infosMajeur = $doctrine->getRepository(InformationMajeur::class)->findOneBy(array('membre_famille' => $membre));

            return new Response($twig->render('back_office/membre_famille/majeur/showInfosMajeur.html.twig', [
                'membre' => $membre,
                'representant' => $representantFamille,
                'infosMajeur' => $infosMajeur,
            ]));
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }

    /**
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/ajouterInfosMajeur", name="Gestionnaire.InfosMajeur.add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addInfosMajeurGEST(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_AJOUT");

        if ( $this->hasDroits($dispositions, "INFOS_AJOUT") )
        {
            $infosMajeur = new InformationMajeur();
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));

            $form = $this->createForm(InfosMajeurType::class, $infosMajeur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $infosMajeur
                    ->setMembreFamille($membre);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosMajeur);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations du majeur ajoutées !');

                return $this->redirectToRoute('Gestionnaire.InfosMajeur.show', ['id' => $representantFamille->getId(), 'idMembre' => $idMembre]);
            }

            return $this->render('back_office/membre_famille/majeur/addInfosMajeur.html.twig', [
                'infosMajeur' => $infosMajeur,
                'membre' => $membre,
                'representant' => $representantFamille,
                'form' => $form->createView(),
            ]);
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }

    /**
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/modifierInfosMajeur", name="Gestionnaire.InfosMajeur.edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editInfosMajeurGEST(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_MODIF");

        if ( $this->hasDroits($dispositions, "INFOS_MODIF") )
        {
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $infosMajeur = $doctrine->getRepository(InformationMajeur::class)->findOneBy(array('membre_famille' => $membre));

            $form = $this->createForm(InfosMajeurType::class, $infosMajeur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosMajeur);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations du majeur modifiées !');

                return $this->redirectToRoute('Gestionnaire.InfosMajeur.show', ['id' => $representantFamille->getId(), 'idMembre' => $idMembre]);
            }

            return $this->render('back_office/membre_famille/majeur/editInfosMajeur.html.twig', [
                'infosMajeur' => $infosMajeur,
                'membre' => $membre,
                'representant' => $representantFamille,
                'form' => $form->createView(),
            ]);
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }

    /**
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/supprimerInfosMajeur", name="Gestionnaire.InfosMajeur.delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteInfosMajeurGEST(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille)
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_SUPPR");

        if ($this->hasDroits($dispositions, "INFOS_SUPPR"))
        {
            $idMembre = $request->get('idMembre');
            $membreFamille = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $infosMajeur = $doctrine->getRepository(InformationMajeur::class)->findOneBy(array('membre_famille' => $membreFamille));

            try {
                if ($infosMajeur != null) {
                    $infosMajeur->setMembreFamille(null);
                }
                $doctrine->getEntityManager()->remove($infosMajeur);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Informations du majeur supprimées !');

            return $this->redirectToRoute('Gestionnaire.InfosMajeur.show', ['id' => $representantFamille->getId(), 'idMembre' => $idMembre]);
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }
}