<?php

namespace App\Controller;

use App\Entity\Dispositions;
use App\Entity\Droits;
use App\Entity\InformationsFamille;
use App\Entity\RepresentantFamille;
use App\Form\InformationFamilleType;
use App\Repository\RepresentantFamilleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

/**
 * @IsGranted("ROLE_USER")
 */
class InfosFamilleController extends AbstractController
{
    /**
     * @Route("/infosFamille", name="InfosFamille.show")
     */
    public function showInfosFamilleUSER(RegistryInterface $doctrine)
    {
        $infosFamille = $doctrine->getRepository(InformationsFamille::class)->findOneBy(['representant_famille'=>$this->getUser()],['id'=>'ASC']);
        return $this->render('front_office/informations_famille/showInfosFamille.html.twig', [
            'infosFamille' => $infosFamille,
        ]);
    }

    /**
     * @Route("/ajouterInfosFamiliales", name="InfosFamille.add")
     */
    public function addInfosFamilleUSER(Request $request, RegistryInterface $doctrine)
    {
        $infosFamille = new InformationsFamille();
        $form = $this->createForm(InformationFamilleType::class, $infosFamille);
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dateActuelle = new \DateTime();
            $infosFamille
                ->setDateModification($dateActuelle)
                ->setRepresentantFamille($representant);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($infosFamille);
            $entityManager->flush();
            $this->addFlash('succes', 'Informations familiales ajoutées !');

            return $this->redirectToRoute('InfosFamille.show');
        }

        return $this->render('front_office/informations_famille/addInfosFamille.html.twig', [
            'infos_famille' => $infosFamille,
            'representant' => $representant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/modifierInfosFamille", name="InfosFamille.edit")
     */
    public function editInfosFamilleUSER(RegistryInterface $doctrine, Request $request)
    {
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        $infosFamille = $doctrine->getRepository(InformationsFamille::class)->findOneBy(array('representant_famille' => $representant));

        $form = $this->createForm(InformationFamilleType::class, $infosFamille);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $dateActuelle = new \DateTime();
            $infosFamille->setDateModification($dateActuelle);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($infosFamille);
            $entityManager->flush();
            $this->addFlash('succes', 'Informations familiales modifiées !');

            return $this->redirectToRoute('InfosFamille.show');
        }

        return $this->render('front_office/informations_famille/editInfosFamille.html.twig', [
            'infos_famille' => $infosFamille,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/supprimerInfosFamille", name="InfosFamille.delete")
     */
    public function deleteInfosFamilleUSER(RegistryInterface $doctrine, Request $request)
    {
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        $infosFamille = $doctrine->getRepository(InformationsFamille::class)->findOneBy(array('representant_famille' => $representant));
        try {
            if ( $infosFamille != null ) { $doctrine->getEntityManager()->remove($infosFamille); }
        } catch (ORMException $e) {
        }
        try {
            $doctrine->getEntityManager()->flush();
        } catch (OptimisticLockException $e) {
        } catch (ORMException $e) {
        }
        $this->addFlash('succes', 'Informations familiales supprimées !');

        return $this->redirectToRoute('InfosFamille.show');
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
     * @Route("/gestionnaire/representant/{id}/informationsFamille", name="Gestionnaire.InfosFamille.show")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showInfosFamilleGEST(RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_VOIR");

        if ( $this->hasDroits($dispositions, "INFOS_VOIR") )
        {
            $infosFamille = $doctrine->getRepository(InformationsFamille::class)->findOneBy(array('representant_famille' => $representantFamille));
            return $this->render('back_office/informations_famille/showInfosFamille.html.twig', [
                'infosFamille' => $infosFamille,
                'representant' => $representantFamille,
            ]);
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }

    /**
     * @Route("/gestionnaire/representant/{id}/ajouterInfosFamiliales", name="Gestionnaire.InfosFamille.add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addInfosFamilleGEST(RegistryInterface $doctrine, Request $request, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_AJOUT");

        if ( $this->hasDroits($dispositions, "INFOS_AJOUT") )
        {
            $infosFamille = new InformationsFamille();
            $form = $this->createForm(InformationFamilleType::class, $infosFamille);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $dateActuelle = new \DateTime();
                $infosFamille
                    ->setDateModification($dateActuelle)
                    ->setRepresentantFamille($representantFamille);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosFamille);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations familiales ajoutées !');

                return $this->redirectToRoute('Gestionnaire.InfosFamille.show', ['id' => $representantFamille->getId()]);
            }

            return $this->render('back_office/informations_famille/addInfosFamille.html.twig', [
                'infos_famille' => $infosFamille,
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
     * @Route("/gestionnaire/representant/{id}/modifierInfosFamille", name="Gestionnaire.InfosFamille.edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editInfosFamilleGEST(RegistryInterface $doctrine, Request $request, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_MODIF");

        if ( $this->hasDroits($dispositions, "INFOS_MODIF") )
        {
            $infosFamille = $doctrine->getRepository(InformationsFamille::class)->findOneBy(array('representant_famille' => $representantFamille));
            $form = $this->createForm(InformationFamilleType::class, $infosFamille);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $dateActuelle = new \DateTime();
                $infosFamille->setDateModification($dateActuelle);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosFamille);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations familiales modifiées !');

                return $this->redirectToRoute('Gestionnaire.InfosFamille.show', ['id' => $representantFamille->getId()]);
            }

            return $this->render('back_office/informations_famille/editInfosFamille.html.twig', [
                'infos_famille' => $infosFamille,
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
     * @Route("/gestionnaire/representant/{id}/supprimerInfosFamille", name="Gestionnaire.InfosFamille.delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteInfosFamilleGEST(RegistryInterface $doctrine, Request $request, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_SUPPR");

        if ( $this->verifierDroits($dispositions, "INFOS_SUPPR") )
        {
            $infosFamille = $doctrine->getRepository(InformationsFamille::class)->findOneBy(array('representant_famille' => $representantFamille));
            try {
                if ($infosFamille != null) {
                    $doctrine->getEntityManager()->remove($infosFamille);
                }
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Informations familiales supprimées !');

            return $this->redirectToRoute('Gestionnaire.InfosFamille.show', ['id' => $representantFamille->getId()]);
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }
}
