<?php

namespace App\Controller;

use App\Entity\InformationMajeur;
use App\Entity\InformationResponsableLegal;
use App\Entity\InformationsMineur;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Bridge\Doctrine\RegistryInterface;   // ORM Doctrine
use Symfony\Component\HttpFoundation\Request;    // objet REQUEST
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/membre")
 */
class MembreFamilleController extends AbstractController
{
    private function validerDonnees($donnees)
    {
        $erreurs = array();
        if ( is_null($donnees['categorie']) ) {
            $erreurs['categorie'] = 'Veuillez indiquer si le nouveau membre est mineur ou majeur.';
        }
        if ( !preg_match("/^[A-Za-z ]{2,}/", $donnees['nom']) ) {
            $erreurs['nom'] = 'Le nom doit être composé de 2 lettres minimum.';
        }
        if ( !preg_match("/^[A-Za-z ]{2,}/", $donnees['prenom']) ) {
            $erreurs['prenom'] = 'Le prénom doit être composé de 2 lettres minimum.';
        }
        if ( is_null($donnees['dateNaissance']) ) {
            $erreurs['dateNaissance'] = 'Veuillez saisir une date valide.';
        }
        return $erreurs;
    }

    /**
     * @Route("/", name="Membre.accueil", methods={"GET"})
     */
    public function index()
    {
        return $this->render('membre_famille/index.html.twig', [
            'controller_name' => 'MembreFamilleController',
        ]);
    }

    /**
     * @Route("/informationsMembre", name="Membre.showMembres", methods={"GET"})
     */
    public function showMembresFamille(Environment $twig, RegistryInterface $doctrine)
    {
        if ( $this->isGranted('ROLE_ADMIN') )
        {
            $membres = $doctrine->getRepository(MembreFamille::class)->findBy([], ['id' => 'ASC']);
            return new Response($twig->render('membre_famille/showMembres.html.twig', ['membresFamille' => $membres]));
        }
        else if ( $this->isGranted('ROLE_USER') )
        {
            $idUser = $this->getUser()->getId();
            $membres = $doctrine->getRepository(MembreFamille::class)->findBy(['representant_famille' => $idUser], ['id' => 'ASC']);
            return new Response($twig->render('membre_famille/showMembres.html.twig', ['membresFamille' => $membres]));
        }
        return $this->redirectToRoute('Membre.accueil');
    }

    /**
     * @Route("/ajouterMembre", name="Membre.addMembre", methods={"GET"})
     */
    public function addMembre(RegistryInterface $doctrine)
    {
        $responsableLegaux = $doctrine->getRepository(InformationResponsableLegal::class)->findBy([], ['id' => 'ASC']);
        return $this->render('membre_famille/addMembre.html.twig', ['responsableLegaux' => $responsableLegaux]);
    }

    /**
     * @Route("/validerAjoutMembre", name="Membre.validAddMembre", methods={"POST"})
     */
    public function validAddMembre(Request $request, RegistryInterface $doctrine)
    {
        if (!$this->isCsrfTokenValid('form_membre_famille', $request->get('token'))) {
            throw new InvalidCsrfTokenException('ERREUR : Clé CSRF invalide');
        }
        $donnees['categorie'] = $request->get('categorie',0)[0];
        $donnees['nom'] = htmlspecialchars($_POST['nom']);
        $donnees['prenom'] = htmlspecialchars($_POST['prenom']);
        $donnees['dateNaissance'] = htmlspecialchars($_POST['dateNaissance']);

        $erreurs = $this->validerDonnees($donnees);

        if ( empty($erreurs) )
        {
            $representantFamille = $this->getUser();
            if ( is_null($representantFamille) ) {
                throw $this->createNotFoundException('ERREUR : Donnée requise non trouvée');
            }
            $dateNaissance = \DateTime::createFromFormat('Y-m-d', $donnees['dateNaissance']);
            $dateMAJ = new \DateTime();
            $membreFamille = new MembreFamille();
            $membreFamille
                ->setNoClient(0)
                ->setCategorie($donnees['categorie'])
                ->setNom($donnees['nom'])
                ->setPrenom($donnees['prenom'])
                ->setDateNaissance($dateNaissance)
                ->setDateMAJ($dateMAJ)
                ->setRepresentantFamille($representantFamille)
                ->setTraitementDonnees(0)
                ->setReglementActivite(0);
            try {
                $doctrine->getEntityManager()->persist($membreFamille);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Membre de famille ajouté !');

            return $this->redirectToRoute('Membre.showMembres');
        }
        $representantFamille = $doctrine->getRepository(RepresentantFamille::class)->findBy([], ['id' => 'ASC']);

        return $this->render('membre_famille/addMembre.html.twig',
            ['donnees' => $donnees,
                'erreurs' => $erreurs,
                'representantFamille' => $representantFamille
            ]);
    }

    /**
     * @Route("/modifierMembre", name="Membre.editMembre", methods={"GET"})
     */
    public function editMembre(Request $request ,RegistryInterface $doctrine)
    {
        $id = $request->get('membre_id');
        $membre = $doctrine->getRepository(MembreFamille::class)->find($id);
        $responsableLegaux = $doctrine->getRepository(InformationResponsableLegal::class)->findBy([], ['id' => 'ASC']);
        return $this->render('membre_famille/editMembre.html.twig', ['donnees' => $membre, 'responsableLegaux' => $responsableLegaux]);
    }
    /**
     * @Route("/ValiderModificationsMembre", name="Membre.validEditMembre", methods={"PUT"})
     */
    public function validerEditMembre(Request $request, RegistryInterface $doctrine)
    {
        if (!$this->isCsrfTokenValid('form_membre_famille', $request->get('token'))) {
            throw new InvalidCsrfTokenException('ERREUR : Clé CSRF invalide');
        }
        $donnees['categorie'] = $request->get('categorie',0)[0];
        $donnees['nom'] = htmlspecialchars($_POST['nom']);
        $donnees['prenom'] = htmlspecialchars($_POST['prenom']);
        $donnees['dateNaissance'] = htmlspecialchars($_POST['dateNaissance']);

        $erreurs = $this->validerDonnees($donnees);

        if ( empty($erreurs) )
        {
            $representantFamille = $this->getUser();
            if ( is_null($representantFamille) ) {
                throw $this->createNotFoundException('ERREUR : Donnée requise non trouvée');
            }
            $dateNaissance = \DateTime::createFromFormat('Y-m-d', $donnees['dateNaissance']);
            $dateMAJ = new \DateTime();
            $id = $request->get('membre_id');
            $membreFamille = $doctrine->getRepository(MembreFamille::class)->find($id);
            var_dump($id);

            $membreFamille
                ->setCategorie($donnees['categorie'])
                ->setNom($donnees['nom'])
                ->setPrenom($donnees['prenom'])
                ->setDateNaissance($dateNaissance)
                ->setDateMAJ($dateMAJ)
                ->setRepresentantFamille($representantFamille)
                ->setTraitementDonnees(0)
                ->setReglementActivite(0);
            try {
                $doctrine->getEntityManager()->persist($membreFamille);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Membre de famille modifié !');

            return $this->redirectToRoute('Membre.showMembres');
        }
        $representantFamille = $doctrine->getRepository(RepresentantFamille::class)->findBy([], ['id' => 'ASC']);

        return $this->render('membre_famille/editMembre.html.twig',
            ['donnees' => $donnees,
                'erreurs' => $erreurs,
                'representantFamille' => $representantFamille
            ]);
    }

    /**
     * @Route("/supprimerMembre", name="Membre.deleteMembre", methods={"DELETE"})
     */
    public function deleteMembre(Request $request, RegistryInterface $doctrine)
    {
        if ( !$this->isCsrfTokenValid('membre_famille_delete', $request->request->get('token')) ) {
            throw new InvalidCsrfTokenException('ERREUR : Clé CSRF invalide');
        }
        $id = $request->request->get('membre_id');
        $membreFamille = $doctrine->getRepository(MembreFamille::class)->find($id);
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

        return $this->redirectToRoute('Membre.showMembres');
    }
}
