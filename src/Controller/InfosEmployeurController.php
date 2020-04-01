<?php

namespace App\Controller;

use App\Entity\Dispositions;
use App\Entity\Droits;
use App\Entity\InformationEmployeur;
use App\Entity\InformationMajeur;
use App\Entity\InformationResponsableLegal;
use App\Entity\InformationsMineur;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
use App\Form\InfosEmployeurType;
use App\Form\MembreFamilleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
class InfosEmployeurController extends AbstractController
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
     * @Route("/membre/{id}/responsable/{idResponsable}/informationsEmployeur", name="InfosEmployeur.show")
     */
    public function showInfosEmployeurUSER(Request $request, Environment $twig, RegistryInterface $doctrine, MembreFamille $membre) : Response
    {
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        $responsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membre));
        $infosEmployeur = $doctrine->getRepository(InformationEmployeur::class)->findOneBy(array('informations_responsable_famille' => $responsable));

        if ( $this->hasRepresentantDroits($doctrine, $representant, $membre) )
        {
            return new Response($twig->render('front_office/infos_employeur/showInfosEmployeur.html.twig', [
                'membre' => $membre,
                'representant' => $this->getUser(),
                'responsable' => $responsable,
                'infosEmployeur' => $infosEmployeur
            ]));
        }
        else
        {
            return $this->render('erreurs/representant_noDroits.html.twig', [
                'representant' => $representant,
                'membre' => $membre,
                'nomOperation' => "voir"
            ]);
        }
    }

    /**
     * @Route("/membre/{id}/responsable/{idResponsable}/ajouterInfosEmployeur", name="InfosEmployeur.add")
     */
    public function addInfosEmployeurUSER(RegistryInterface $doctrine, Request $request, MembreFamille $membre) : Response
    {
        $infosEmployeur = new InformationEmployeur();
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        $responsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(['membre_famille' => $membre]);
        if ( $this->hasRepresentantDroits($doctrine, $representant, $membre) )
        {
            $form = $this->createForm(InfosEmployeurType::class, $infosEmployeur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $infosEmployeur
                    ->setInformationsResponsableFamille($responsable);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosEmployeur);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations de l\'entreprise ajoutées !');

                return $this->redirectToRoute('InfosEmployeur.show', ['id' => $membre->getId(), 'idResponsable' => $responsable->getId()]);
            }

            return $this->render('front_office/infos_employeur/addInfosEmployeur.html.twig', [
                'infosEmployeur' => $infosEmployeur,
                'responsable' => $responsable,
                'membre' => $membre,
                'representant' => $this->getUser(),
                'form' => $form->createView(),
            ]);
        }
        else
        {
            return $this->render('erreurs/representant_noDroits.html.twig', [
                'representant' => $representant,
                'membre' => $membre,
                'nomOperation' => "ajouter"
            ]);
        }
    }

    /**
     * @Route("/membre/{id}/responsable/{idResponsable}/modifierInfosEmployeur", name="InfosEmployeur.edit")
     */
    public function editInfosEmployeurUSER(Request $request, RegistryInterface $doctrine, MembreFamille $membre) : Response
    {
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        $responsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(['membre_famille' => $membre]);
        $infosEmployeur = $doctrine->getRepository(InformationEmployeur::class)->findOneBy(['informations_responsable_famille' => $responsable]);
        if ( $this->hasRepresentantDroits($doctrine, $representant, $membre) )
        {
            $form = $this->createForm(InfosEmployeurType::class, $infosEmployeur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $infosEmployeur
                    ->setInformationsResponsableFamille($responsable);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosEmployeur);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations de l\'entreprise modifiées !');

                return $this->redirectToRoute('InfosEmployeur.show', ['id' => $membre->getId(), 'idResponsable' => $responsable->getId()]);
            }

            return $this->render('front_office/infos_employeur/editInfosEmployeur.html.twig', [
                'infosEmployeur' => $infosEmployeur,
                'responsable' => $responsable,
                'membre' => $membre,
                'representant' => $this->getUser(),
                'form' => $form->createView(),
            ]);
        }
        else
        {
            return $this->render('erreurs/representant_noDroits.html.twig', [
                'representant' => $representant,
                'membre' => $membre,
                'nomOperation' => "modifier"
            ]);
        }
    }

    /**
     * @Route("/membre/{id}/responsable/{idResponsable}/supprimerInfosEmployeur", name="InfosEmployeur.delete")
     */
    public function deleteInfosEmployeurUSER(Request $request, RegistryInterface $doctrine, MembreFamille $membre)
    {
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        $responsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(['membre_famille' => $membre]);
        $infosEmployeur = $doctrine->getRepository(InformationEmployeur::class)->findOneBy(['informations_responsable_famille' => $responsable]);
        if ( $this->hasRepresentantDroits($doctrine, $representant, $membre) )
        {
            try {
                if ( $infosEmployeur != null )
                {
                    $infosEmployeur->setInformationsResponsableFamille(null);
                }
                $doctrine->getEntityManager()->remove($infosEmployeur);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Informations de l\'employeur supprimées !');

            return $this->redirectToRoute('InfosEmployeur.show', ['id' => $membre->getId(), 'idResponsable' => $responsable->getId()]);
        }
        else
        {
            return $this->render('erreurs/representant_noDroits.html.twig', [
                'representant' => $representant,
                'membre' => $membre,
                'nomOperation' => "supprimer"
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
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/responsable/{idResponsable}/informationsEmployeur", name="Gestionnaire.InfosEmployeur.show")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showInfosEmployeurGEST(Request $request, Environment $twig, RegistryInterface $doctrine, RepresentantFamille $representant) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_VOIR");

        if ( $this->hasDroits($dispositions, "INFOS_VOIR") )
        {
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representant, 'id' => $idMembre));
            $idResponsable = $request->get('idResponsable');
            $responsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membre, 'id' => $idResponsable));
            $infosEmployeur = $doctrine->getRepository(InformationEmployeur::class)->findOneBy(array('informations_responsable_famille' => $responsable));

            return new Response($twig->render('back_office/infos_employeur/showInfosEmployeur.html.twig', [
                'membre' => $membre,
                'representant' => $representant,
                'responsable' => $responsable,
                'infosEmployeur' => $infosEmployeur
            ]));
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }

    /**
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/responsable/{idResponsable}/ajouterInfosEmployeur", name="Gestionnaire.InfosEmployeur.add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addInfosEmployeurGEST(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_AJOUT");

        if ( $this->hasDroits($dispositions, "INFOS_AJOUT") )
        {
            $infosEmployeur = new InformationEmployeur();
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $idResponsable = $request->get('idResponsable');
            $responsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membre, 'id' => $idResponsable));

            $form = $this->createForm(InfosEmployeurType::class, $infosEmployeur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $infosEmployeur
                    ->setInformationsResponsableFamille($responsable);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosEmployeur);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations de l\'employeur ajoutées !');

                return $this->redirectToRoute('Gestionnaire.InfosEmployeur.show', ['id' => $representantFamille->getId(), 'idMembre' => $idMembre, 'idResponsable' => $idResponsable]);
            }

            return $this->render('back_office/infos_employeur/addInfosEmployeur.html.twig', [
                'infosEmployeur' => $infosEmployeur,
                'responsable' => $responsable,
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
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/responsable/{idResponsable}/modifierInfosEmployeur", name="Gestionnaire.InfosEmployeur.edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editInfosEmployeurGEST(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_MODIF");

        if ( $this->hasDroits($dispositions, "INFOS_MODIF") )
        {
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $idResponsable = $request->get('idResponsable');
            $responsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membre, 'id' => $idResponsable));
            $infosEmployeur = $doctrine->getRepository(InformationEmployeur::class)->findOneBy(array('informations_responsable_famille' => $responsable));

            $form = $this->createForm(InfosEmployeurType::class, $infosEmployeur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $infosEmployeur
                    ->setInformationsResponsableFamille($responsable);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosEmployeur);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations de l\'employeur modifiées !');

                return $this->redirectToRoute('Gestionnaire.InfosEmployeur.show', ['id' => $representantFamille->getId(), 'idMembre' => $idMembre, 'idResponsable' => $idResponsable]);
            }

            return $this->render('back_office/infos_employeur/editInfosEmployeur.html.twig', [
                'responsable' => $responsable,
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
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/responsable/{idResponsable}/supprimerInfosEmployeur", name="Gestionnaire.InfosEmployeur.delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteInfosEmployeurGEST(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille)
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_SUPPR");

        if ($this->hasDroits($dispositions, "INFOS_SUPPR"))
        {
            $idMembre = $request->get('idMembre');
            $membreFamille = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $idResponsable = $request->get('idResponsable');
            $responsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membreFamille, 'id' => $idResponsable));
            $infosEmployeur = $doctrine->getRepository(InformationEmployeur::class)->findOneBy(array('informations_responsable_famille' => $responsable));

            try {
                if ( $infosEmployeur != null )
                {
                    $infosEmployeur->setInformationsResponsableFamille(null);
                }
                $doctrine->getEntityManager()->remove($infosEmployeur);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Informations de l\'employeur supprimées !');

            return $this->redirectToRoute('Gestionnaire.InfosEmployeur.show', ['id' => $representantFamille->getId(), 'idMembre' => $idMembre, 'idResponsable' => $idResponsable]);
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }
}
