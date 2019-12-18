<?php

namespace App\DataFixtures;

use App\Entity\InformationMajeur;
use App\Entity\InformationsMineur;
use App\Entity\MembreFamille;
use App\Entity\RepresentantFamille;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadRepresentantsFamilles($manager);
        $this->loadMembresFamilles($manager);
        $this->loadDroits($manager);
    }

    public function loadRepresentantsFamilles(ObjectManager $manager)
    {
        $rf = [
          ['id' => 1, 'login' => 'Thomas', 'motdepasse' => '123456', 'nom' => 'Mattone', 'prenom' => 'Thomas'
          , 'adresse' => 'Belfort', 'nomobile' => '0707070707', 'nofixe' => '0494777777', 'mail' => 'thomas@mattone'
          , 'datenaissance' => '2000-01-06', 'datefinadhesion' => '2019-12-02'],
          ['id' => 2, 'login' => 'Zoe', 'motdepasse' => '123456', 'nom' => 'Siena', 'prenom' => 'Zoe'
            , 'adresse' => 'Hyeres', 'nomobile' => '0606060606', 'nofixe' => '0494666666', 'mail' => 'zoe@siena'
            , 'datenaissance' => '2002-07-02', 'datefinadhesion' => '2020-02-02']
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
            $manager->flush();
        }
    }

    public function loadMembresFamilles(ObjectManager $manager)
    {
        $if = [
          ['id' => 1, 'noClient' => 'A', 'categorie' => 'Adulte', 'nom' => 'Mattone', 'prenom' => 'Thomas'
            , 'datenaissance' => '2000-01-06', 'reglement_activite' => 1, 'traitementdonnees' => 1
            , 'id_representantfamille' => 1, 'dateMAJ' => '2019-01-01'],
          ['id' => 2, 'noClient' => 'B', 'categorie' => 'Adulte', 'nom' => 'Sienna', 'prenom' => 'Zoe'
            , 'datenaissance' => '2002-07-02', 'reglement_activite' => 1, 'traitementdonnees' => 1
            , 'id_representantfamille' => 2, 'dateMAJ' => '2019-01-01'],
          ['id' => 3, 'noClient' => 'B1', 'categorie' => 'Enfant', 'nom' => 'Sienna', 'prenom' => 'Louise'
            , 'datenaissance' => '2007-06-06', 'reglement_activite' => 1, 'traitementdonnees' => 1
            , 'id_representantfamille' => 2, 'dateMAJ' => '2019-01-01'],
          ['id' => 4, 'noClient' => 'B2', 'categorie' => 'Enfant', 'nom' => 'Sienna', 'prenom' => 'Simon'
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
            if ($new_membre->getCategorie() == 'Adulte') {
                $inf_maj = new InformationMajeur();
                $mail = $new_membre->getPrenom() . '@' . $new_membre->getNom();
                $inf_maj->setMail($mail);
                $inf_maj->setCommunicationResponsableLegal(1);
                $new_membre->setInformationMajeur($inf_maj);
            }
            elseif ($new_membre->getCategorie() == 'Enfant') {
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
