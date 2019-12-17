<?php

namespace App\Controller;

use App\Entity\Droits;
use App\Entity\Gestionnaires;
use App\Entity\InformationResponsableLegal;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
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
 * @Route("/gestionnaire")
 * @IsGranted("ROLE_ADMIN")
 */
class GestionnaireController extends AbstractController
{
    private function validerDonneesRepresentant($donnees)
    {
        $erreurs = array();
        if ( !preg_match("/^[A-Za-z ]{2,}/", $donnees['login']) ) {
            $erreurs['login'] = 'Le login doit être composé de 2 lettres minimum.';
        }
        if ( !preg_match("/^[A-Za-z ]{2,}/", $donnees['nom']) ) {
            $erreurs['nom'] = 'Le nom doit être composé de 2 lettres minimum.';
        }
        if ( !preg_match("/^[A-Za-z ]{2,}/", $donnees['prenom']) ) {
            $erreurs['prenom'] = 'Le prénom doit être composé de 2 lettres minimum.';
        }
/*        if ( !preg_match("/^[A-Za-z ]{2,}/", $donnees['adresse']) ) {
            $erreurs['adresse'] = 'L\'adresse doit être composée de 2 lettres minimum.';
        }*/
        if ( !preg_match("/[0-9]{8}/", $donnees['noMobile']) ) {
            $erreurs['noMobile'] = 'Veuillez saisir un numéro de mobile valide de 8 chiffres.';
        }
        if ( !preg_match("/[0-9]{8}/", $donnees['noFixe']) ) {
            $erreurs['noFixe'] = 'Veuillez saisir un numéro de fixe valide de 8 chiffres.';
        }
        if ( is_null($donnees['dateNaissance']) ) {
            $erreurs['dateNaissance'] = 'Veuillez saisir une date de naissance valide.';
        }
        if ( is_null($donnees['dateFinAdhesion']) ) {
            $erreurs['dateFinAdhesion'] = 'Veuillez saisir une date de fin d\'adhésion valide.';
        }
        if ( is_null($donnees['estActive']) ) {
        $erreurs['estActive'] = 'Veuillez indiquer si le représentant a activé le compte.';
    }
        return $erreurs;
    }

    /**
     * @Route("/", name="Gestionnaire.accueil", methods={"GET"})
     */
    public function index()
    {
        return $this->render('gestionnaire/espace.html.twig');
    }

    /**
     * @Route("/listeGestionnaires", name="Gestionnaire.showListeGestionnaires", methods={"GET"})
     */
    public function showListeGestionnaires(Environment $twig, RegistryInterface $doctrine)
    {
        $gestionnaires = $doctrine->getRepository(Gestionnaires::class)->findBy([],['id'=>'ASC']);
        return new Response($twig->render('gestionnaire/listeGestionnaires.html.twig', ['gestionnaires' => $gestionnaires]));
    }

    /**
     * @Route("/listeFamilles", name="Gestionnaire.showListeFamilles", methods={"GET"})
     */
    public function showListeFamilles( Environment $twig, RegistryInterface $doctrine)
    {
        $familles = $doctrine->getRepository(RepresentantFamille::class)->findBy([],['id'=>'ASC']);
        return new Response($twig->render('gestionnaire/listeFamilles.html.twig', ['familles' => $familles]));
    }

    /**
     * @Route("/droits", name="Gestionnaire.showDroits", methods={"GET"})
     */
    public function showDroits(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        $droits = $doctrine->getRepository(Droits::class)->findBy([],['id'=>'ASC']);
        return new Response($twig->render('gestionnaire/droits.html.twig', ['droits' => $droits]));
    }


    /**
     * @Route("/modifierRepresentant", name="Gestionnaire.editRepresentant", methods={"GET"})
     */
    public function editRepresentant(Request $request ,RegistryInterface $doctrine)
    {
        $id = $request->get('representant_id');
        $representant = $doctrine->getRepository(RepresentantFamille::class)->find($id);
        return $this->render('gestionnaire/editRepresentant.html.twig', ['donnees' => $representant]);
    }
    /**
     * @Route("/ValiderModificationsRepresentant", name="Gestionnaire.validEditRepresentant", methods={"PUT"})
     */
    public function validerEditRepresentant(Request $request, RegistryInterface $doctrine)
    {
        if (!$this->isCsrfTokenValid('form_representant', $request->get('token'))) {
            throw new InvalidCsrfTokenException('ERREUR : Clé CSRF invalide');
        }
        $donnees['id'] = $request->get('representant_id');
        $donnees['login'] = htmlspecialchars($_POST['login']);
        $donnees['nom'] = htmlspecialchars($_POST['nom']);
        $donnees['prenom'] = htmlspecialchars($_POST['prenom']);
        $donnees['adresse'] = htmlspecialchars($_POST['adresse']);
        $donnees['noMobile'] = htmlspecialchars($_POST['noMobile']);
        $donnees['noFixe'] = htmlspecialchars($_POST['noFixe']);
        $donnees['mail'] = htmlspecialchars($_POST['mail']);
        $donnees['dateNaissance'] = htmlspecialchars($_POST['dateNaissance']);
        $donnees['dateFinAdhesion'] = htmlspecialchars($_POST['dateFinAdhesion']);
        $donnees['estActive'] = $request->get('estActive',0)[0];

        $erreurs = $this->validerDonneesRepresentant($donnees);

        if ( empty($erreurs) )
        {
            $dateNaissance = \DateTime::createFromFormat('Y-m-d', $donnees['dateNaissance']);
            $dateFinAdhesion = \DateTime::createFromFormat('Y-m-d', $donnees['dateFinAdhesion']);
            var_dump($dateNaissance);
            $representantFamille = $doctrine->getRepository(RepresentantFamille::class)->find($donnees['id']);

            $representantFamille
                ->setLogin($donnees['login'])
                ->setNom($donnees['nom'])
                ->setPrenom($donnees['prenom'])
                ->setAdresse($donnees['adresse'])
                ->setNoMobile($donnees['noMobile'])
                ->setNoFixe($donnees['noFixe'])
                ->setMail($donnees['mail'])
                ->setDateNaissance($dateNaissance)
                ->setDateFinAdhesion($dateFinAdhesion)
                ->setEstActive($donnees['estActive']);
            try {
                $doctrine->getEntityManager()->persist($representantFamille);
            } catch (ORMException $e) {
            }
            try {
                $doctrine->getEntityManager()->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            $this->addFlash('succes', 'Représentant modifié !');

            return $this->redirectToRoute('Gestionnaire.showListeFamilles');
        }

        return $this->render('gestionnaire/editRepresentant.html.twig',
            ['donnees' => $donnees, 'erreurs' => $erreurs]);
    }

    /**
     * @Route("/supprimerRepresentant", name="Gestionnaire.deleteRepresentant", methods={"DELETE"})
     */
    public function deleteRepresentant(RegistryInterface $doctrine, Request $request)
    {
        if ( !$this->isCsrfTokenValid('representant_delete', $request->request->get('token')) ) {
            throw new InvalidCsrfTokenException('ERREUR : Clé CSRF invalide');
        }
        $id = $request->request->get('representant_id');
        $representantFamille = $doctrine->getRepository(RepresentantFamille::class)->find($id);
        $membreFamille = $doctrine->getRepository(MembreFamille::class)->findOneBy(array('representant_famille' => $representantFamille));
        if ( $membreFamille != null )
        {
            $membreFamille->setRepresentantFamille(null);
        }
        try {
            $doctrine->getEntityManager()->remove($representantFamille);
        } catch (ORMException $e) {
        }
        try {
            $doctrine->getEntityManager()->flush();
        } catch (OptimisticLockException $e) {
        } catch (ORMException $e) {
        }
        $this->addFlash('succes', 'Représentant supprimé !');

        return $this->redirectToRoute('Gestionnaire.showListeFamilles');
    }

    private function validerDonneesMembre($donnees)
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
     * @Route("/informationsMembre", name="Gestionnaire.showMembres", methods={"GET"})
     */
    public function showMembresFamille(Environment $twig, RegistryInterface $doctrine)
    {
        $membres = $doctrine->getRepository(MembreFamille::class)->findBy([], ['id' => 'ASC']);
        return new Response($twig->render('membre_famille/showMembres.html.twig', ['membresFamille' => $membres]));
    }

    /**
     * @Route("/ajouterMembre", name="Gestionnaire.addMembre", methods={"GET"})
     */
    public function addMembre(RegistryInterface $doctrine)
    {
        $responsableLegaux = $doctrine->getRepository(InformationResponsableLegal::class)->findBy([], ['id' => 'ASC']);
        $representants = $doctrine->getRepository(RepresentantFamille::class)->findBy([], ['id' => 'ASC']);
        return $this->render('membre_famille/addMembre.html.twig', ['responsableLegaux' => $responsableLegaux, 'representants' => $representants]);
    }

    /**
     * @Route("/validerAjoutMembre", name="Gestionnaire.validAddMembre", methods={"POST"})
     */
    public function validAddMembre(Request $request, RegistryInterface $doctrine)
    {
        if (!$this->isCsrfTokenValid('form_membre_famille', $request->get('token'))) {
            throw new InvalidCsrfTokenException('ERREUR : Clé CSRF invalide');
        }
        $donnees['representant_id'] = htmlspecialchars($_POST['representant_id']);
        $donnees['categorie'] = $request->get('categorie',0)[0];
        $donnees['nom'] = htmlspecialchars($_POST['nom']);
        $donnees['prenom'] = htmlspecialchars($_POST['prenom']);
        $donnees['dateNaissance'] = htmlspecialchars($_POST['dateNaissance']);

        $erreurs = $this->validerDonneesMembre($donnees);

        if ( empty($erreurs) )
        {
            $representantFamille = $doctrine->getRepository(RepresentantFamille::class)->find($donnees['representant_id']);
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

            return $this->redirectToRoute('Gestionnaire.showMembres');
        }
        $representantFamille = $doctrine->getRepository(RepresentantFamille::class)->findBy([], ['id' => 'ASC']);

        return $this->render('membre_famille/addMembre.html.twig',
            ['donnees' => $donnees,
                'erreurs' => $erreurs,
                'representants' => $representantFamille
            ]);
    }

    /**
     * @Route("/modifierMembre", name="Gestionnaire.editMembre", methods={"GET"})
     */
    public function editMembre(Request $request, RegistryInterface $doctrine)
    {
        $id = $request->get('membre_id');
        $membre = $doctrine->getRepository(MembreFamille::class)->find($id);
        $responsableLegaux = $doctrine->getRepository(InformationResponsableLegal::class)->findBy([], ['id' => 'ASC']);
        $representants = $doctrine->getRepository(RepresentantFamille::class)->findBy([], ['id' => 'ASC']);
        return $this->render('membre_famille/editMembre.html.twig', ['donnees' => $membre, 'responsableLegaux' => $responsableLegaux, 'representants' => $representants]);
    }
    /**
     * @Route("/ValiderModificationsMembre", name="Gestionnaire.validEditMembre", methods={"PUT"})
     */
    public function validerEditMembre(Request $request, RegistryInterface $doctrine)
    {
        if (!$this->isCsrfTokenValid('form_membre_famille', $request->get('token'))) {
            throw new InvalidCsrfTokenException('ERREUR : Clé CSRF invalide');
        }
        $donnees['representant_id'] = htmlspecialchars($_POST['representant_id']);
        $donnees['id'] = $request->get('membre_id');
        $donnees['categorie'] = $request->get('categorie',0)[0];
        $donnees['nom'] = htmlspecialchars($_POST['nom']);
        $donnees['prenom'] = htmlspecialchars($_POST['prenom']);
        $donnees['dateNaissance'] = htmlspecialchars($_POST['dateNaissance']);

        $erreurs = $this->validerDonneesMembre($donnees);

        if ( empty($erreurs) )
        {
            $representantFamille = $doctrine->getRepository(RepresentantFamille::class)->find($donnees['representant_id']);
            $dateNaissance = \DateTime::createFromFormat('Y-m-d', $donnees['dateNaissance']);
            $dateMAJ = new \DateTime();
            var_dump($dateMAJ);
            $membreFamille = $doctrine->getRepository(MembreFamille::class)->find($donnees['id']);

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
            $this->addFlash('succes', 'Membre modifié !');

            return $this->redirectToRoute('Gestionnaire.showMembres');
        }
        $representantFamille = $doctrine->getRepository(RepresentantFamille::class)->findBy([], ['id' => 'ASC']);

        return $this->render('membre_famille/editMembre.html.twig',
            ['donnees' => $donnees,
                'erreurs' => $erreurs,
                'representantFamille' => $representantFamille
            ]);
    }

    /**
     * @Route("/supprimerMembre", name="Gestionnaire.deleteMembre", methods={"DELETE"})
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

        return $this->redirectToRoute('Gestionnaire.showMembres');
    }
}