<?php

namespace App\DataFixtures;

use App\Entity\Gestionnaires;
use App\Entity\InformationMajeur;
use App\Entity\InformationsMineur;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Droits;

use App\Repository\RepresentantFamilleRepository;
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
use App\Entity\InformationsFamille;
use App\Form\RepresentantFamilleType;
use App\Form\InformationFamilleType;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //$this->loadRepresentantsFamilles($manager);
        //$this->loadMembresFamilles($manager);
        // $this->loadDroits($manager);

        $this->loadSuperAdmin($manager);
    }



        public function loadSuperAdmin(ObjectManager $manager)
    {
        $rf = [
            ['id' => 1, 'nom' => 'superadmin', 'prenom' => 'superadmin', 'motdepasse' => '$2y$13$FiOcaFSVSpAKWIFbRUdt5e0vZc66iksotns/0hhGZ.660vth9m1a6'],


        ];


        foreach ($rf as $r) {
            $new_rf = new Gestionnaires();
            $gestionnaire = new Gestionnaires();
            $new_rf->setNom($r['nom']);
            $new_rf->setPrenom($r['prenom']);
            $new_rf->setMotdepasse($r['motdepasse']);
            $manager->persist($new_rf);
            $manager->flush();
        }
    }

    public function loadMembresFamilles(ObjectManager $manager)
    {
        $if = [
          ['id' => 1, 'noClient' => 'A', 'categorie' => 'Majeur', 'nom' => 'Mattone', 'prenom' => 'Thomas'
            , 'datenaissance' => '2000-01-06', 'reglement_activite' => 1, 'traitementdonnees' => 1
            , 'id_representantfamille' => 1, 'dateMAJ' => '2019-01-01'],
          ['id' => 2, 'noClient' => 'B', 'categorie' => 'Majeur', 'nom' => 'Sienna', 'prenom' => 'Zoe'
            , 'datenaissance' => '2002-07-02', 'reglement_activite' => 1, 'traitementdonnees' => 1
            , 'id_representantfamille' => 2, 'dateMAJ' => '2019-01-01'],
          ['id' => 3, 'noClient' => 'B1', 'categorie' => 'Mineur', 'nom' => 'Sienna', 'prenom' => 'Louise'
            , 'datenaissance' => '2007-06-06', 'reglement_activite' => 1, 'traitementdonnees' => 1
            , 'id_representantfamille' => 2, 'dateMAJ' => '2019-01-01'],
          ['id' => 4, 'noClient' => 'B2', 'categorie' => 'Mineur', 'nom' => 'Sienna', 'prenom' => 'Simon'
            , 'datenaissance' => '2001-11-11', 'reglement_activite' => 1, 'traitementdonnees' => 1
            , 'id_representantfamille' => 2, 'dateMAJ' => '2019-01-01']
        ];

        foreach ($if as $i) {
            $new_membre = new MembreFamille();
            $new_membre->setNoClient($i['noClient']);
            $new_membre->setCategorie($i['categorie']);
            $new_membre->setNom($i['nom']);
            $new_membre->setPrenom($i['prenom']);
            $new_membre->setDateNaissance(new \DateTime($i['datenaissance']));
            $new_membre->setReglementActivite($i['reglement_activite']);
            $new_membre->setTraitementDonnees($i['traitementdonnees']);
            $new_membre->setDateMAJ(new \DateTime($i['dateMAJ']));
            $rep = $manager->getRepository(RepresentantFamille::class)->find($i['id_representantfamille']);
            $new_membre->setRepresentantFamille($rep);
            // check adult or child
            if ($new_membre->getCategorie() == 'Majeur') {
                $inf_maj = new InformationMajeur();
                $mail = $new_membre->getPrenom() . '@' . $new_membre->getNom();
                $inf_maj->setMail($mail);
                $inf_maj->setCommunicationResponsableLegal(1);
                $new_membre->setInformationMajeur($inf_maj);
            }
            elseif ($new_membre->getCategorie() == 'Mineur') {
                $inf_min = new InformationsMineur();
                $inf_min->setAutorisationTransport(1);
                $inf_min->setDroitImage(1);
                $inf_min->setAutorisationTransportMedical(1);
                $inf_min->setAutorisationSortieSeul(1);
                $new_membre->setInformationsMineur($inf_min);
            }
            $manager->persist($new_membre);
            $manager->flush();
        }
    }

    public function loadDroits()
    {
       /*créer / modifier / désactiver des comptes
    voir / exporter les dossiers adhérent
    créer / modifier les dossiers adhérents
        $droits = [
            ['id' => 1, '' => , '' => ],
            ['id' => 2, '' => , '' =>],
            ['id' => 3, '' => , '' =>]
        ];

        foreach ($rf as $r) {
            $new_rf = new RepresentantFamille();
            $new_rf->setLogin($r['login']);
            $new_rf->setMotdepasse($r['motdepasse']);
            $new_rf->setNom($r['nom']);
            $new_rf->setPrenom($r['prenom']);
            $new_rf->setAdresse($r['adresse']);
            $new_rf->setNoMobile($r['nomobile']);
            $new_rf->setNoFixe($r['nofixe']);
            $new_rf->setMail($r['mail']);
            $new_rf->setDateNaissance(new \DateTime($r['datenaissance']));
            $new_rf->setDateFinAdhesion(new \DateTime($r['datefinadhesion']));
            $new_rf->setEstActive(1);
            $manager->persist($new_rf);
            $manager->flush();*/
    }
}
