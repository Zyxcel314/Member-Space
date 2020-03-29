<?php

namespace App\Controller;

use App\Entity\Dispositions;
use App\Entity\Droits;
use App\Entity\InformationMajeur;
use App\Entity\InformationResponsableLegal;
use App\Entity\InformationsMineur;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
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
class MembreFamilleController extends AbstractController
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
     * @Route("/membre", name="Membre.show", methods={"GET"})
     */
    public function showMembresUSER(RegistryInterface $doctrine)
    {
        $idUser = $this->getUser()->getId();
        $membres = $doctrine->getRepository(MembreFamille::class)->findBy(['representant_famille' => $idUser], ['id' => 'ASC']);
        $date = new \DateTime(date('Y-m-d'));
        date_sub($date, date_interval_create_from_date_string('18 years'));
        return $this->render('front_office/membre_famille/showMembres.html.twig', ['membresFamille' => $membres, 'dateMajorite'=>$date]);
    }

    /**
     * @Route("/membre/ajouterMembre", name="Membre.add")
     */
    public function addMembreUSER(RegistryInterface $doctrine, Request $request)
    {
        $idRepresentant = $this->getUser()->getId();
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($idRepresentant);
        $membre = new MembreFamille();
        $form = $this->createForm(MembreFamilleType::class, $membre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dateNaissance = $membre->getDateNaissance();
            $dateActuelle = new \DateTime(date('Y-m-d'));
            $dateMajorite = date_sub($dateActuelle, date_interval_create_from_date_string('18 years'));
            // Majeur
            if ( $dateNaissance < $dateMajorite ) {
                $membre
                    ->setCategorie('Majeur')
                    ->setNoClient($idRepresentant . 'MJ');
                // Mineur
            } else {
                $membre
                    ->setCategorie('Mineur')
                    ->setNoClient($idRepresentant . 'MN');
            }
            $dateActuelle = new \DateTime(date('Y-m-d'));
            $membre
                ->setTraitementDonnees(0)
                ->setReglementActivite(0)
                ->setRepresentantFamille($representant)
                ->setDateMAJ($dateActuelle);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($membre);
            $entityManager->flush();
            $this->addFlash('succes', 'Membre ajouté !');

            return $this->redirectToRoute('Membre.show');
        }

        return $this->render('front_office/membre_famille/addMembre.html.twig', [
            'membreFamille' => $membre,
            'representant' => $representant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/membre/{id}/modifierMembre", name="Membre.edit")
     */
    public function editMembreUSER(Request $request, RegistryInterface $doctrine, MembreFamille $membreFamille) : Response
    {
        $idRepresentant = $this->getUser()->getId();
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($idRepresentant);
        //$responsableLegaux = $doctrine->getRepository(InformationResponsableLegal::class)->findBy([], ['id' => 'ASC']);

        if ( $this->hasRepresentantDroits($doctrine, $representant, $membreFamille) )
        {
            $form = $this->createForm(MembreFamilleType::class, $membreFamille);
            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid())
            {
                $dateNaissance = $membreFamille->getDateNaissance();
                $dateActuelle = new \DateTime(date('Y-m-d'));
                $dateMajorite = date_sub($dateActuelle, date_interval_create_from_date_string('18 years'));
                if ( $dateNaissance < $dateMajorite ) {
                    $membreFamille
                        ->setCategorie('Majeur')
                        ->setNoClient($idRepresentant . 'MJ');
                } else {
                    $membreFamille
                        ->setCategorie('Mineur')
                        ->setNoClient($idRepresentant . 'MN');
                }
                $dateActuelle = new \DateTime(date('Y-m-d'));
                $membreFamille
                    ->setRepresentantFamille($representant)
                    ->setDateMAJ($dateActuelle);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($membreFamille);
                $entityManager->flush();
                $this->addFlash('succes', 'Membre modifié !');

                return $this->redirectToRoute('Membre.show');
            }

            return $this->render('front_office/membre_famille/editMembre.html.twig', [
                'membreFamille' => $membreFamille,
                'representant' => $representant,
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
     * @Route("/membre/{id}/supprimerMembre", name="Membre.delete")
     */
    public function deleteMembreUSER(Request $request, RegistryInterface $doctrine, MembreFamille $membreFamille) : Response
    {
        if ( !$this->isCsrfTokenValid('membre_famille_delete', $request->request->get('token')) ) {
            throw new InvalidCsrfTokenException('ERREUR : Clé CSRF invalide');
        }
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($this->getUser()->getId());
        if ( $this->hasRepresentantDroits($doctrine, $representant, $membreFamille) )
        {
            $responsableLegal = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membreFamille));
            if ( $responsableLegal != null )
            {
                $responsableLegal->setMembreFamille(null);
            }
            try {
                $doctrine->getEntityManager()->remove($membreFamille);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Membre supprimé !');

            return $this->redirectToRoute('Membre.show');
        }
        else
        {
            return $this->render('erreurs/representant_noDroits.html.twig', [
                'representant' => $representant,
                'membre' => $membreFamille,
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
     * @Route("/gestionnaire/representant/{id}/informationsMembres", name="Gestionnaire.Membres.show")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showMembresGEST(Environment $twig, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_VOIR");

        if ( $this->verifierDroits($dispositions, "INFOS_VOIR") )
        {
            $membres = $doctrine->getRepository(MembreFamille::class)->findBy(['representant_famille' => $representantFamille], ['id' => 'ASC']);
            $dateActuelle = new \DateTime(date('Y-m-d'));
            $dateMajorite = date_sub($dateActuelle, date_interval_create_from_date_string('18 years'));
            return new Response($twig->render('back_office/membre_famille/showMembres.html.twig', [
                'membresFamille' => $membres,
                'dateMajorite' => $dateMajorite,
                'representant' => $representantFamille,
            ]));
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }

    /**
     * @Route("/gestionnaire/representant/{id}/ajouterMembre", name="Gestionnaire.Membre.add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addMembreGEST(RegistryInterface $doctrine, Request $request, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_AJOUT");

        if ( $this->hasDroits($dispositions, "INFOS_AJOUT") ) {
            $membre = new MembreFamille();
            $form = $this->createForm(MembreFamilleType::class, $membre);
            $form->handleRequest($request);
            $idRepresentant = $representantFamille->getId();

            if ($form->isSubmitted() && $form->isValid()) {
                $dateNaissance = $membre->getDateNaissance();
                $dateActuelle = new \DateTime(date('Y-m-d'));
                $dateMajorite = date_sub($dateActuelle, date_interval_create_from_date_string('18 years'));
                // Majeur
                if ($dateNaissance < $dateMajorite) {
                    $membre
                        ->setCategorie('Majeur')
                        ->setNoClient($idRepresentant . 'MJ');
                    // Mineur
                } else {
                    $membre
                        ->setCategorie('Mineur')
                        ->setNoClient($idRepresentant . 'MN');
                }
                $dateActuelle = new \DateTime(date('Y-m-d'));
                $membre
                    ->setTraitementDonnees(0)
                    ->setReglementActivite(0)
                    ->setRepresentantFamille($representantFamille)
                    ->setDateMAJ($dateActuelle);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($membre);
                $entityManager->flush();
                $this->addFlash('succes', 'Membre ajouté !');

                return $this->redirectToRoute('Gestionnaire.Membres.show', ['id' => $idRepresentant]);
            }

            return $this->render('back_office/membre_famille/addMembre.html.twig', [
                'membreFamille' => $membre,
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
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/modifierMembre", name="Gestionnaire.Membre.edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editMembreGEST(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_MODIF");

        if ( $this->hasDroits($dispositions, "INFOS_MODIF") )
        {
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $responsableLegaux = $doctrine->getRepository(InformationResponsableLegal::class)->findBy([], ['id' => 'ASC']);
            $idRepresentant = $representantFamille->getId();

            $form = $this->createForm(MembreFamilleType::class, $membre)
                ->add('traitementDonnees')
                ->add('reglement_activite')
                ->add('exportDonnees');
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $dateNaissance = $membre->getDateNaissance();
                $dateActuelle = new \DateTime(date('Y-m-d'));
                $dateMajorite = date_sub($dateActuelle, date_interval_create_from_date_string('18 years'));
                if ($dateNaissance < $dateMajorite) {
                    $membre
                        ->setCategorie('Majeur')
                        ->setNoClient($idRepresentant . 'MJ');
                } else {
                    $membre
                        ->setCategorie('Mineur')
                        ->setNoClient($idRepresentant . 'MN');
                }
                $dateActuelle = new \DateTime(date('Y-m-d'));
                $membre
                    ->setRepresentantFamille($representantFamille)
                    ->setDateMAJ($dateActuelle);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($membre);
                $entityManager->flush();
                $this->addFlash('succes', 'Membre modifié !');

                return $this->redirectToRoute('Gestionnaire.Membres.show', ['id' => $idRepresentant]);
            }

            return $this->render('back_office/membre_famille/editMembre.html.twig', [
                'membreFamille' => $membre,
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
     * @Route("/gestionnaire/representant/{id}//membre/{idMembre}/supprimerMembre", name="Gestionnaire.Membre.delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteMembreGEST(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille)
    {
//        if ( !$this->isCsrfTokenValid('membre_famille_delete', $request->request->get('token')) ) {
//            throw new InvalidCsrfTokenException('ERREUR : Clé CSRF invalide');
//        }
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_SUPPR");

        if ($this->hasDroits($dispositions, "INFOS_SUPPR"))
        {
            $idMembre = $request->get('idMembre');
            $membreFamille = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $responsableLegal = $doctrine->getRepository(InformationResponsableLegal::class)->findOneBy(array('membre_famille' => $membreFamille));

            if ($responsableLegal != null) {
                $responsableLegal->setMembreFamille(null);
            }
            try {
                $doctrine->getEntityManager()->remove($membreFamille);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Membre supprimé !');

            return $this->redirectToRoute('Gestionnaire.Membres.show', ['id' => $representantFamille->getId()]);
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }
}
