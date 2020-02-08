<?php
namespace App\DataFixtures;

use App\Entity\Dispositions;
use App\Entity\Droits;
use App\Entity\Gestionnaires;
use App\Entity\InformationMajeur;
use App\Entity\InformationResponsableLegal;
use App\Entity\InformationsMineur;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
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
        /* Pour chaque compte, le mot de passe est 'motdepasse' */
        /* ACHTUNG : Il ne faut faire qu'une seule fois le load de tout ce bordel :
            si vous loadez une seconde fois, décalage au niveau des ids et du coup liens entre entités non faits
            -> Purger la base de donner à chaque load
        */
        $this->loadSuperAdmin($manager);
        $this->loadGestionnaires($manager);
        $this->loadDroits($manager);
        $this->loadDispositions($manager);

        $this->loadRepresentantsFamilles($manager);
        $this->loadInfosFamille($manager);
        $this->loadMembresFamilles($manager);
    }

    public function loadSuperAdmin(ObjectManager $manager)
    {
        $superAdmin = [
            ['id' => 1, 'mail' => 'SUPER_ADMIN@gmail.fr', 'nom' => 'SUPER_ADMIN', 'prenom' => 'PRENOM_SUPER_ADMIN',
                'motdepasse' => '$2y$13$zW/YYlmBo9.9TaEi0L7wq./cV7Sbjvj7S0.Wu.PyOR0Qu8goTPljW',
                'idGoogleAuth' => 'ID_GOOGLE_SUPER_ADMIN']
        ];

        foreach ($superAdmin as $admin)
        {
            $newSuperAdmin = new Gestionnaires();
            $newSuperAdmin
                ->setMail($admin['mail'])
                ->setNom($admin['nom'])
                ->setPrenom($admin['prenom'])
                ->setMotdepasse($admin['motdepasse']);
            $manager->persist($newSuperAdmin);
        }
    }

    public function loadDroits(ObjectManager $manager)
    {
        $droits = [
            // Droits du SUPER_ADMIN
            ['id' => 1, 'code' => 'DROITS_SUPER_ADMIN', 'libelle' => 'Droits du SUPER_ADMIN'],

            // Partie représentant de famille
            ['id' => 2, 'code' => 'RP_VOIR', 'libelle' => 'Consulter un compte adhérent'],
            ['id' => 3, 'code' => 'RP_AJOUT', 'libelle' => 'Créer un compte adhérent'],
            ['id' => 4, 'code' => 'RP_MODIF', 'libelle' => 'Modifier un compte adhérent'],
            ['id' => 5, 'code' => 'RP_SUPPR', 'libelle' => 'Supprimer un compte adhérent'],

            // Toutes les autres tables en gros
            ['id' => 6, 'code' => 'INFOS_VOIR', 'libelle' => 'Consulter les dossiers des adhérents'],
            ['id' => 7, 'code' => 'INFOS_AJOUT', 'libelle' => 'Créer les dossiers des adhérents'],
            ['id' => 8, 'code' => 'INFOS_MODIF', 'libelle' => 'Modifier les dossiers des adhérents'],
            ['id' => 9, 'code' => 'INFOS_SUPPR', 'libelle' => 'Supprimer les dossiers des adhérent'],

            ['id' => 10, 'code' => 'INFOS_EXPORT', 'libelle' => 'Exporter les infos des adhérents'],
        ];

        foreach ($droits as $droit)
        {
            $newDroit = new Droits();
            $newDroit
                ->setCode($droit['code'])
                ->setLibelle($droit['libelle']);
            $manager->persist($newDroit);
            $manager->flush();
        }
    }

    public function loadGestionnaires(ObjectManager $manager)
    {
        $gestionnaires = [
            ['id' => 2, 'mail' => 'Gestionnaire1@gmail.fr', 'nom' => 'Gestionnaire1', 'prenom' => 'PRENOM_GEST_1',
                'motdepasse' => '$2y$13$zW/YYlmBo9.9TaEi0L7wq./cV7Sbjvj7S0.Wu.PyOR0Qu8goTPljW',
                'idGoogleAuth' => 'ID_GOOGLE_GEST_1'],
            ['id' => 3, 'mail' => 'Gestionnaire2@gmail.fr', 'nom' => 'Gestionnaire2', 'prenom' => 'PRENOM_GEST_2',
                'motdepasse' => '$2y$13$zW/YYlmBo9.9TaEi0L7wq./cV7Sbjvj7S0.Wu.PyOR0Qu8goTPljW',
                'idGoogleAuth' => 'ID_GOOGLE_GEST_2'],
            ['id' => 4, 'mail' => 'Gestionnaire3@gmail.fr', 'nom' => 'Gestionnaire3', 'prenom' => 'PRENOM_GEST_3',
                'motdepasse' => '$2y$13$zW/YYlmBo9.9TaEi0L7wq./cV7Sbjvj7S0.Wu.PyOR0Qu8goTPljW',
                'idGoogleAuth' => 'ID_GOOGLE_GEST_3']
        ];

        foreach ($gestionnaires as $gestionnaire)
        {
            $newGestionnaire = new Gestionnaires();
            $newGestionnaire
                ->setMail($gestionnaire['mail'])
                ->setNom($gestionnaire['nom'])
                ->setPrenom($gestionnaire['prenom'])
                ->setMotdepasse($gestionnaire['motdepasse'])
                ->setIdGoogleAuth($gestionnaire['idGoogleAuth']);

            $manager->persist($newGestionnaire);
            $manager->flush();
        }
    }

    public function loadDispositions(ObjectManager $manager)
    {
        $dispositions = [
            // Droits du SUPER_ADMIN
            ['id' => 1, 'id_gestionnaire' => 1, 'id_droits' => 1],

            // Droits du Gestionnaire1
            ['id' => 2, 'id_gestionnaire' => 2, 'id_droits' => 2],
            ['id' => 3, 'id_gestionnaire' => 2, 'id_droits' => 3],
            ['id' => 4, 'id_gestionnaire' => 2, 'id_droits' => 4],
            ['id' => 5, 'id_gestionnaire' => 2, 'id_droits' => 5],
            ['id' => 6, 'id_gestionnaire' => 2, 'id_droits' => 6],
            ['id' => 7, 'id_gestionnaire' => 2, 'id_droits' => 7],
            ['id' => 8, 'id_gestionnaire' => 2, 'id_droits' => 8],
            ['id' => 9, 'id_gestionnaire' => 2, 'id_droits' => 9],
            ['id' => 10, 'id_gestionnaire' => 2, 'id_droits' => 10],

            // Droits du Gestionnaire2
            ['id' => 11, 'id_gestionnaire' => 3, 'id_droits' => 2],
            ['id' => 12, 'id_gestionnaire' => 3, 'id_droits' => 6],
            ['id' => 13, 'id_gestionnaire' => 3, 'id_droits' => 7],
            ['id' => 14, 'id_gestionnaire' => 3, 'id_droits' => 8],
            ['id' => 15, 'id_gestionnaire' => 3, 'id_droits' => 9],

            // Droits du Gestionnaire3
            ['id' => 16, 'id_gestionnaire' => 4, 'id_droits' => 2],
            ['id' => 17, 'id_gestionnaire' => 4, 'id_droits' => 6],
            ['id' => 18, 'id_gestionnaire' => 4, 'id_droits' => 7],
        ];

        foreach ($dispositions as $disposition)
        {
            $gestionnaire = $manager->getRepository(Gestionnaires::class)->find(['id' => $disposition['id_gestionnaire']]);
            $droits = $manager->getRepository(Droits::class)->find(['id' => $disposition['id_droits']]);
            $newDispositions = new Dispositions();
            if ( $gestionnaire ) {
                $newDispositions->setGestionnaire($gestionnaire);
            }
            if ( $droits ) {
                $newDispositions->setDroits($droits);
            }
            $manager->persist($newDispositions);
            $manager->flush();
        }
    }

    public function loadRepresentantsFamilles(ObjectManager $manager)
    {
        $representants = [
            ['id ' => 1, 'login' => 'Thomas', 'motdepasse' => '$2y$13$zW/YYlmBo9.9TaEi0L7wq./cV7Sbjvj7S0.Wu.PyOR0Qu8goTPljW',
              'nom' => 'MATTONE', 'prenom' => 'Thomas', 'adresse' => 'Belfort', 'nomobile' => '01010101', 'nofixe' => '01111111' , 'mail' => 'thomas@gmail.com',
              'datenaissance' => '2000-01-01', 'datefinadhesion' => '2020-01-01'],
            ['id ' => 2, 'login' => 'Zaid', 'motdepasse' => '$2y$13$zW/YYlmBo9.9TaEi0L7wq./cV7Sbjvj7S0.Wu.PyOR0Qu8goTPljW',
            'nom' => 'ALAA HAZID', 'prenom' => 'Zaid', 'adresse' => 'Belfort', 'nomobile' => '02020202', 'nofixe' => '02222222' , 'mail' => 'zaid@gmail.com',
            'datenaissance' => '2000-01-01', 'datefinadhesion' => '2020-01-01'],
            ['id ' => 3, 'login' => 'Nathan', 'motdepasse' => '$2y$13$zW/YYlmBo9.9TaEi0L7wq./cV7Sbjvj7S0.Wu.PyOR0Qu8goTPljW',
                'nom' => 'JAUGEY', 'prenom' => 'Nathan', 'adresse' => 'Belfort', 'nomobile' => '03030303', 'nofixe' => '03333333' , 'mail' => 'nathan@gmail.com',
                'datenaissance' => '2000-01-01', 'datefinadhesion' => '2020-01-01'],
            ['id ' => 4, 'login' => 'Brayan', 'motdepasse' => '$2y$13$zW/YYlmBo9.9TaEi0L7wq./cV7Sbjvj7S0.Wu.PyOR0Qu8goTPljW',
                'nom' => 'BIOUT', 'prenom' => 'Brayan', 'adresse' => 'Belfort', 'nomobile' => '04040404', 'nofixe' => '04444444' , 'mail' => 'brayan@gmail.com',
                'datenaissance' => '2000-01-01', 'datefinadhesion' => '2020-01-01'],
            ['id ' => 5, 'login' => 'Adam', 'motdepasse' => '$2y$13$zW/YYlmBo9.9TaEi0L7wq./cV7Sbjvj7S0.Wu.PyOR0Qu8goTPljW',
                'nom' => 'MEGNAI', 'prenom' => 'Adam', 'adresse' => 'Belfort', 'nomobile' => '05050505', 'nofixe' => '05555555' , 'mail' => 'adam@gmail.com',
                'datenaissance' => '2000-01-01', 'datefinadhesion' => '2020-01-01'],
            ['id ' => 6, 'login' => 'Ted', 'motdepasse' => '$2y$13$zW/YYlmBo9.9TaEi0L7wq./cV7Sbjvj7S0.Wu.PyOR0Qu8goTPljW',
                'nom' => 'PERROS', 'prenom' => 'Ted', 'adresse' => 'Villars les Paumés', 'nomobile' => '06060606', 'nofixe' => '06666666' , 'mail' => 'ted@hotmail.fr',
                'datenaissance' => '2000-01-01', 'datefinadhesion' => '2020-01-01']
        ];

        foreach ($representants as $representant)
        {
            $newRepresentant = new RepresentantFamille();
            $newRepresentant
                ->setLogin($representant['login'])
                ->setMotdepasse($representant['motdepasse'])
                ->setNom($representant['nom'])
                ->setPrenom($representant['prenom'])
                ->setAdresse($representant['adresse'])
                ->setNoMobile($representant['nomobile'])
                ->setNoFixe($representant['nofixe'])
                ->setMail($representant['mail'])
                ->setDateNaissance(new \DateTime($representant['datenaissance']))
                ->setDateFinAdhesion(new \DateTime($representant['datefinadhesion']))
                ->setMailTokenVerification('0')
                ->setEstActive(1);

            $manager->persist($newRepresentant);
            $manager->flush();
        }
    }

    public function loadInfosFamille(ObjectManager $manager)
    {
        $infosFamille = [
            ['id' => 1, 'dateModification' => '2020-01-01', 'noAllocataire' => 'NO_ALLOCATAIRE_RP_1', 'nomCAF' => 'NOM_CAF_RP_1', 'nbEnfants' => 2, 'estMonoparentale' => 0, 'regimeProtectionSociale' => 'RG_PRT_RP1', 'id_representantFamille' => 1],
            ['id' => 2, 'dateModification' => '2020-01-01', 'noAllocataire' => 'NO_ALLOCATAIRE_RP_2', 'nomCAF' => 'NOM_CAF_RP_2', 'nbEnfants' => 2, 'estMonoparentale' => 1, 'regimeProtectionSociale' => 'RG_PRT_RP2', 'id_representantFamille' => 2],
            ['id' => 3, 'dateModification' => '2020-01-01', 'noAllocataire' => 'NO_ALLOCATAIRE_RP_3', 'nomCAF' => 'NOM_CAF_RP_3', 'nbEnfants' => 2, 'estMonoparentale' => 0, 'regimeProtectionSociale' => 'RG_PRT_RP3', 'id_representantFamille' => 3],
            ['id' => 4, 'dateModification' => '2020-01-01', 'noAllocataire' => 'NO_ALLOCATAIRE_RP_4', 'nomCAF' => 'NOM_CAF_RP_4', 'nbEnfants' => 0, 'estMonoparentale' => 1, 'regimeProtectionSociale' => 'RG_PRT_RP4', 'id_representantFamille' => 4],
            ['id' => 5, 'dateModification' => '2020-01-01', 'noAllocataire' => 'NO_ALLOCATAIRE_RP_5', 'nomCAF' => 'NOM_CAF_RP_5', 'nbEnfants' => 0, 'estMonoparentale' => 0, 'regimeProtectionSociale' => 'RG_PRT_RP5', 'id_representantFamille' => 5],
            ['id' => 6, 'dateModification' => '2020-01-01', 'noAllocataire' => 'NO_ALLOCATAIRE_RP_6', 'nomCAF' => 'NOM_CAF_RP_6', 'nbEnfants' => 0, 'estMonoparentale' => 0, 'regimeProtectionSociale' => 'RG_PRT_RP6', 'id_representantFamille' => 6],
        ];

        foreach ($infosFamille as $info)
        {
            $representant = $manager->getRepository(RepresentantFamille::class)->find(['id' => $info['id_representantFamille']]);
            $newInfosFamille = new InformationsFamille();

            if( $representant ) {
                $newInfosFamille->setRepresentantFamille($representant);
            }
            $newInfosFamille
                ->setDateModification(new \DateTime($info['dateModification']))
                ->setNoAllocataire($info['noAllocataire'])
                ->setNomCAF($info['nomCAF'])
                ->setNbEnfants($info['nbEnfants'])
                ->setEstMonoparentale($info['estMonoparentale'])
                ->setRegimeProtectionSociale($info['regimeProtectionSociale']);

            $manager->persist($newInfosFamille);
            $manager->flush();
        }
    }

    public function loadMembresFamilles(ObjectManager $manager)
    {
        $membres = [
            // Chaque représentant de famille doit avoir son propre membre avec ses indications
            ['id' => 1, 'noClient' => '1RP', 'categorie' => 'Majeur', 'nom' => 'MATTONE', 'prenom' => 'Thomas', 'dateNaissance' => '01-01-2000', 'traitementsDonnees' => 0, 'dateMAJ' => '01-01-2019', 'reglementActivite' => 1, 'id_representantFamille' => 1, 'mail' => 'thomas@gmail.com',],
            ['id' => 2, 'noClient' => '2RP', 'categorie' => 'Majeur', 'nom' => 'ALAA HAZID', 'prenom' => 'Zaid', 'dateNaissance' => '01-01-2000', 'traitementsDonnees' => 0, 'dateMAJ' => '01-01-2019', 'reglementActivite' => 1, 'id_representantFamille' => 2, 'mail' => 'zaid@gmail.com'],
            ['id' => 3, 'noClient' => '3RP', 'categorie' => 'Majeur', 'nom' => 'JAUGEY', 'prenom' => 'Nathan', 'dateNaissance' => '01-01-2000', 'traitementsDonnees' => 0, 'dateMAJ' => '01-01-2019', 'reglementActivite' => 1, 'id_representantFamille' => 3, 'mail' => 'nathan@gmail.com'],
            ['id' => 4, 'noClient' => '4RP', 'categorie' => 'Majeur', 'nom' => 'BIOUT', 'prenom' => 'Brayan', 'dateNaissance' => '01-01-2000', 'traitementsDonnees' => 0, 'dateMAJ' => '01-01-2019', 'reglementActivite' => 1, 'id_representantFamille' => 4, 'mail' => 'brayan@gmail.com'],
            ['id' => 5, 'noClient' => '5RP', 'categorie' => 'Majeur', 'nom' => 'MEGNAI', 'prenom' => 'Adam', 'dateNaissance' => '01-01-2000', 'traitementsDonnees' => 0, 'dateMAJ' => '01-01-2019', 'reglementActivite' => 1, 'id_representantFamille' => 5, 'mail' => 'adam@gmail.com'],
            ['id' => 6, 'noClient' => '6RP', 'categorie' => 'Majeur', 'nom' => 'PERROS', 'prenom' => 'Ted', 'dateNaissance' => '01-01-2000', 'traitementsDonnees' => 0, 'dateMAJ' => '01-01-2019', 'reglementActivite' => 1, 'id_representantFamille' => 6, 'mail' => 'ted@hotmail.fr'],

            // Enfants (mineur + majeur) de Thomas
            ['id' => 7, 'noClient' => '1MN', 'categorie' => 'Mineur', 'nom' => 'MATTONE', 'prenom' => 'PRENOM_GOSSE_1', 'dateNaissance' => '01-01-2000', 'traitementsDonnees' => 0, 'dateMAJ' => '01-01-2019', 'reglementActivite' => 1, 'id_representantFamille' => 1],
            ['id' => 8, 'noClient' => '1MJ', 'categorie' => 'Majeur', 'nom' => 'MATTONE', 'prenom' => 'PRENOM_GOSSE_2', 'dateNaissance' => '01-01-2010', 'traitementsDonnees' => 0, 'dateMAJ' => '01-01-2019', 'reglementActivite' => 1, 'id_representantFamille' => 1, 'mail' => 'PRENOM_GOSSE_2@gmail.com'],

            // Enfants (mineurs uniquement) de Zaid
            ['id' => 9, 'noClient' => '2MN', 'categorie' => 'Mineur', 'nom' => 'ALAA HAZID', 'prenom' => 'PRENOM_GOSSE_1', 'dateNaissance' => '01-01-2010', 'traitementsDonnees' => 0, 'dateMAJ' => '01-01-2019', 'reglementActivite' => 1, 'id_representantFamille' => 2],
            ['id' => 10, 'noClient' => '2MN', 'categorie' => 'Mineur', 'nom' => 'ALAA HAZID', 'prenom' => 'PRENOM_GOSSE_2', 'dateNaissance' => '01-01-2010', 'traitementsDonnees' => 0, 'dateMAJ' => '01-01-2019', 'reglementActivite' => 1, 'id_representantFamille' => 2],

            // Enfants (majeur uniquement) de Nathan
            ['id' => 11, 'noClient' => '3MJ', 'categorie' => 'Majeur', 'nom' => 'JAUGEY', 'prenom' => 'PRENOM_GOSSE_1', 'dateNaissance' => '01-01-2000', 'traitementsDonnees' => 0, 'dateMAJ' => '01-01-2019', 'reglementActivite' => 1, 'id_representantFamille' => 3, 'mail' => 'PRENOM_GOSSE_1@gmail.com'],
            ['id' => 12, 'noClient' => '3MJ', 'categorie' => 'Majeur', 'nom' => 'JAUGEY', 'prenom' => 'PRENOM_GOSSE_2', 'dateNaissance' => '01-01-2000', 'traitementsDonnees' => 0, 'dateMAJ' => '01-01-2019', 'reglementActivite' => 1, 'id_representantFamille' => 3, 'mail' => 'PRENOM_GOSSE_2@gmail.com'],
        ];

        foreach ($membres as $membre)
        {
            $representant = $manager->getRepository(RepresentantFamille::class)->find(['id' => $membre['id_representantFamille']]);
            $newMembre = new MembreFamille();

            if( $representant ) {
                $newMembre->setRepresentantFamille($representant);
            }
            $newMembre
                ->setNoClient($membre['noClient'])
                ->setCategorie($membre['categorie'])
                ->setNom($membre['nom'])
                ->setPrenom($membre['prenom'])
                ->setDateNaissance(new \DateTime($membre['dateNaissance']))
                ->setDateMAJ(new \DateTime($membre['dateMAJ']))
                ->setTraitementDonnees($membre['traitementsDonnees'])
                ->setReglementActivite($membre['reglementActivite']);

                if ($newMembre->getCategorie() == 'Majeur')
                {
                    //echo 'MARCHE 1';
                    $responsableLegal = new InformationResponsableLegal();
                    echo $responsableLegal->getId();
                    $responsableLegal
                        ->setProfession('JOB');
                        //->setMembreFamille($newMembre);
                    $manager->persist($responsableLegal);
                    $manager->flush();

                    $infosMajeur = new InformationMajeur();
                    $infosMajeur
                        ->setMail($membre['mail'])
                        ->setCommunicationResponsableLegal(1)
                        ->setMembreFamille($newMembre);
                    $manager->persist($infosMajeur);
                    $manager->flush();
                    //$newMembre->setInformationMajeur($infosMajeur);

                }
                elseif ($newMembre->getCategorie() == 'Mineur')
                {
                    //echo 'MARCHE 2';
                    $infosMineur = new InformationsMineur();
                    $infosMineur->setAutorisationTransport(1)
                        ->setDroitImage(1)
                        ->setAutorisationTransportMedical(1)
                        ->setAutorisationSortieSeul(1)
                        ->setMembreFamille($newMembre);
                    $manager->persist($infosMineur);
                    $manager->flush();
                    //$newMembre->setInformationsMineur($infosMineur);
                }

            $manager->persist($newMembre);
            $manager->flush();
        }
    }
}