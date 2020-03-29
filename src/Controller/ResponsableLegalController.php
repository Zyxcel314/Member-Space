<?php

namespace App\Controller;

use App\Entity\Dispositions;
use App\Entity\Droits;
use App\Entity\InformationResponsableLegal;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
use App\Form\InfosResponsableLegalType;
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
class ResponsableLegalController extends AbstractController
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
     * @Route("/membre/{id}/informationsResponsable", name="InfosResponsable.show")
     */
    public function showInfosResponsableUSER(Request $request, Environment $twig, RegistryInterface $doctrine, MembreFamille $membreFamille) : Response
    {
        $responsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membreFamille));
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        if ( $this->hasRepresentantDroits($doctrine, $representant, $membreFamille) )
        {
            return new Response($twig->render('front_office/responsable_legal/showInfosResponsableLegal.html.twig', [
                'membre' => $membreFamille,
                'representant' => $this->getUser(),
                'responsable' => $responsable,
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
     * @Route("/membre/{id}/ajouterInfosResponsable", name="InfosResponsable.add")
     */
    public function addInfosResponsableUSER(RegistryInterface $doctrine, Request $request, MembreFamille $membreFamille) : Response
    {
        $responsable = new InformationResponsableLegal();
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        if ( $this->hasRepresentantDroits($doctrine, $representant, $membreFamille) )
        {

            $form = $this->createForm(InfosResponsableLegalType::class, $responsable);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $responsable
                    ->setMembreFamille($membreFamille);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($responsable);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations du responsable ajoutées !');

                return $this->redirectToRoute('InfosResponsable.show', ['id' => $membreFamille->getId()]);
            }

            return $this->render('front_office/responsable_legal/addInfosResponsableLegal.html.twig', [
                'responsable' => $responsable,
                'membre' => $membreFamille,
                'representant' => $this->getUser(),
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
     * @Route("/membre/{id}/modifierInfosResponsable", name="InfosResponsable.edit")
     */
    public function editInfosResponsableUSER(Request $request, RegistryInterface $doctrine, MembreFamille $membreFamille) : Response
    {
        $responsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membreFamille));
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        if ( $this->hasRepresentantDroits($doctrine, $representant, $membreFamille) )
        {

            $form = $this->createForm(InfosResponsableLegalType::class, $responsable);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($responsable);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations du responsable modifiées !');

                return $this->redirectToRoute('InfosResponsable.show', ['id' => $membreFamille->getId()]);
            }

            return $this->render('front_office/responsable_legal/editInfosResponsableLegal.html.twig', [
                'responsable' => $responsable,
                'membre' => $membreFamille,
                'representant' => $this->getUser(),
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
     * @Route("/membre/{id}/supprimerInfosResponsable", name="InfosResponsable.delete")
     */
    public function deleteInfosResponsableUSER(Request $request, RegistryInterface $doctrine, MembreFamille $membreFamille)
    {
        $responsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membreFamille));
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        if ( $this->hasRepresentantDroits($doctrine, $representant, $membreFamille) )
        {

            try {
                if ( $responsable != null )
                {
                    $responsable->setMembreFamille(null);
                }
                $doctrine->getEntityManager()->remove($responsable);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Informations du reponsable supprimées !');

            return $this->redirectToRoute('InfosResponsable.show', ['id' => $membreFamille->getId()]);
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
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/informationsResponsable", name="Gestionnaire.InfosResponsable.show")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showInfosResponsableGEST(Request $request, Environment $twig, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_VOIR");

        if ( $this->hasDroits($dispositions, "INFOS_VOIR") )
        {
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $responsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membre));

            return new Response($twig->render('back_office/responsable_legal/showInfosResponsableLegal.html.twig', [
                'membre' => $membre,
                'representant' => $representantFamille,
                'responsable' => $responsable,
            ]));
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }

    /**
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/ajouterInfosResponsable", name="Gestionnaire.InfosResponsable.add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addInfosResponsableGEST(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_AJOUT");

        if ( $this->hasDroits($dispositions, "INFOS_AJOUT") )
        {
            $infosResponsable = new InformationResponsableLegal();
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));

            $form = $this->createForm(InfosResponsableLegalType::class, $infosResponsable);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $infosResponsable
                    ->setMembreFamille($membre);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosResponsable);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations du responsable légal ajoutées !');

                return $this->redirectToRoute('Gestionnaire.InfosResponsable.show', ['id' => $representantFamille->getId(), 'idMembre' => $idMembre]);
            }

            return $this->render('back_office/responsable_legal/addInfosResponsableLegal.html.twig', [
                'responsable' => $infosResponsable,
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
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/modifierInfosResponsable", name="Gestionnaire.InfosResponsable.edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editInfosResponsableLegal(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_MODIF");

        if ( $this->hasDroits($dispositions, "INFOS_MODIF") )
        {
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $infosResponsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membre));

            $form = $this->createForm(InfosResponsableLegalType::class, $infosResponsable);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosResponsable);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations du responsable légal modifiées !');

                return $this->redirectToRoute('Gestionnaire.InfosResponsable.show', ['id' => $representantFamille->getId(), 'idMembre' => $idMembre]);
            }

            return $this->render('back_office/responsable_legal/editInfosResponsableLegal.html.twig', [
                'responsable' => $infosResponsable,
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
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/supprimerInfosResponsable", name="Gestionnaire.InfosResponsable.delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteInfosResponsableLegal(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille)
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_SUPPR");

        if ($this->hasDroits($dispositions, "INFOS_SUPPR"))
        {
            $idMembre = $request->get('idMembre');
            $membreFamille = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $infosResponsable = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membreFamille));

            try {
                if ($infosResponsable != null) {
                    $infosResponsable->setMembreFamille(null);
                }
                $doctrine->getEntityManager()->remove($infosResponsable);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Informations du responsable légal supprimées !');

            return $this->redirectToRoute('Gestionnaire.InfosResponsable.show', ['id' => $representantFamille->getId(), 'idMembre' => $idMembre]);
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }
}
