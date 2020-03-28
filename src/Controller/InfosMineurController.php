<?php

namespace App\Controller;

use App\Entity\Dispositions;
use App\Entity\Droits;
use App\Entity\InformationMajeur;
use App\Entity\InformationResponsableLegal;
use App\Entity\InformationsMineur;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

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
use Symfony\Component\String\Slugger\AsciiSlugger;
/**
 * @IsGranted("ROLE_USER")
 */
class InfosMineurController extends AbstractController
{
    /**
     * @Route("/membre/{id}/informationsMineur", name="InfosMineur.show")
     */
    public function showInfosMineurUSER(Request $request, Environment $twig, RegistryInterface $doctrine, MembreFamille $membreFamille) : Response
    {
        $infosMineur = $doctrine->getRepository(InformationsMineur::class)->findOneBy(array('membre_famille' => $membreFamille));
        return new Response($twig->render('front_office/membre_famille/mineur/showInfosMineur.html.twig', [
            'membre' => $membreFamille,
            'representant' => $this->getUser()->getId(),
            'infosMineur' => $infosMineur,
        ]));
    }

    /**
     * @Route("/membre/{id}/ajouterInfosMineur", name="InfosMineur.add")
     */
    public function addInfosMineurUSER(Request $request, MembreFamille $membreFamille) : Response
    {
        $infosMineur = new InformationsMineur();

        $form = $this->createForm(InfosMineurType::class, $infosMineur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $fichier = $form->get('ficheSanitaires')->getData();
            $slugger = new AsciiSlugger();

            if ($fichier) {
                $originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$fichier->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $fichier->move(
                        $this->getParameter('ficheSanitaires_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $infosMineur->addFicheSanitaire($newFilename);
            }

            $infosMineur
                ->setMembreFamille($membreFamille);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($infosMineur);
            $entityManager->flush();
            $this->addFlash('succes', 'Informations du mineur ajoutées !');

            return $this->redirectToRoute('InfosMineur.show', ['id' => $membreFamille->getId()]);
        }

        return $this->render('front_office/membre_famille/mineur/addInfosMineur.html.twig', [
            'infosMineur' => $infosMineur,
            'membre' => $membreFamille,
            'representant' => $this->getUser()->getId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/membre/{id}/modifierInfosMineur", name="InfosMineur.edit")
     */
    public function editInfosMineurUSER(Request $request, RegistryInterface $doctrine, MembreFamille $membreFamille) : Response
    {
        $infosMineur = $doctrine->getRepository(InformationsMineur::class)->findOneBy(array('membre_famille' => $membreFamille));

        $form = $this->createForm(InfosMineurType::class, $infosMineur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($infosMineur);
            $entityManager->flush();
            $this->addFlash('succes', 'Informations du mineur modifiées !');

            return $this->redirectToRoute('InfosMineur.show', ['id' => $membreFamille->getId()]);
        }

        return $this->render('front_office/membre_famille/mineur/editInfosMineur.html.twig', [
            'infosMineur' => $infosMineur,
            'membre' => $membreFamille,
            'representant' => $this->getUser()->getId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/membre/{id}/supprimerInfosMineur", name="InfosMineur.delete")
     */
    public function deleteInfosMineurUSER(Request $request, RegistryInterface $doctrine, MembreFamille $membreFamille)
    {
        $infosMineur = $doctrine->getRepository(InformationsMineur::class)->findOneBy(array('membre_famille' => $membreFamille));

        try {
            if ( $infosMineur != null )
            {
                $infosMineur->setMembreFamille(null);
            }
            $doctrine->getEntityManager()->remove($infosMineur);
        } catch (ORMException $e) {
        }
        try {
            $doctrine->getEntityManager()->flush();
        } catch (OptimisticLockException $e) {
        } catch (ORMException $e) {
        }
        $this->addFlash('succes', 'Informations du mineur supprimées !');

        return $this->redirectToRoute('InfosMineur.show', ['id' => $membreFamille->getId()]);
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
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/informationsMineur", name="Gestionnaire.InfosMineur.show")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showInfosMineurGEST(Request $request, Environment $twig, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_VOIR");

        if ( $this->hasDroits($dispositions, "INFOS_VOIR") )
        {
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $infosMineur = $doctrine->getRepository(InformationsMineur::class)->findOneBy(array('membre_famille' => $membre));;
            return new Response($twig->render('back_office/membre_famille/mineur/showInfosMineur.html.twig', [
                'membre' => $membre,
                'representant' => $representantFamille,
                'infosMineur' => $infosMineur,
            ]));
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }

    /**
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/ajouterInfosMineur", name="Gestionnaire.InfosMineur.add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addInfosMineurGEST(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_AJOUT");

        if ( $this->hasDroits($dispositions, "INFOS_AJOUT") )
        {
            $infosMineur = new InformationsMineur();
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));

            $form = $this->createForm(InfosMineurType::class, $infosMineur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $infosMineur
                    ->setMembreFamille($membre);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosMineur);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations du mineur ajoutées !');

                return $this->redirectToRoute('Gestionnaire.InfosMineur.show', ['id' => $representantFamille->getId(), 'idMembre' => $idMembre]);
            }

            return $this->render('back_office/membre_famille/mineur/addInfosMineur.html.twig', [
                'infosMineur' => $infosMineur,
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
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/modifierInfosMineur", name="Gestionnaire.InfosMineur.edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editInfosMineurGEST(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille) : Response
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_MODIF");

        if ( $this->hasDroits($dispositions, "INFOS_MODIF") )
        {
            $idMembre = $request->get('idMembre');
            $membre = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $infosMineur = $doctrine->getRepository(InformationsMineur::class)->findOneBy(array('membre_famille' => $membre));

            $form = $this->createForm(InfosMineurType::class, $infosMineur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($infosMineur);
                $entityManager->flush();
                $this->addFlash('succes', 'Informations du mineur modifiées !');

                return $this->redirectToRoute('Gestionnaire.InfosMineur.show', ['id' => $representantFamille->getId(), 'idMembre' => $idMembre]);
            }

            return $this->render('back_office/membre_famille/mineur/editInfosMineur.html.twig', [
                'infosMineur' => $infosMineur,
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
     * @Route("/gestionnaire/representant/{id}/membre/{idMembre}/supprimerInfosMineur", name="Gestionnaire.InfosMineur.delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteInfosMineurGEST(Request $request, RegistryInterface $doctrine, RepresentantFamille $representantFamille)
    {
        $dispositions = $doctrine->getRepository(Dispositions::class)->findBy(['gestionnaire' => $this->getUser()]);
        $listeDroits = $doctrine->getRepository(Droits::class)->findAll();
        $droitNecessaire = $this->droitNecessaire($listeDroits, "INFOS_SUPPR");

        if ($this->hasDroits($dispositions, "INFOS_SUPPR"))
        {
            $idMembre = $request->get('idMembre');
            $membreFamille = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille, 'id' => $idMembre));
            $infosMineur = $doctrine->getRepository(InformationsMineur::class)->findOneBy(array('membre_famille' => $membreFamille));

            try {
                if ($infosMineur != null) {
                    $infosMineur->setMembreFamille(null);
                }
                $doctrine->getEntityManager()->remove($infosMineur);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Informations du mineur supprimées !');

            return $this->redirectToRoute('Gestionnaire.InfosMineur.show', ['id' => $representantFamille->getId(), 'idMembre' => $idMembre]);
        }
        return $this->render('erreurs/gestionnaire_noDroits.html.twig', [
            'connected_gestionnaire' => $this->getUser(),
            'droitNecessaire' => $droitNecessaire,
        ]);
    }
}