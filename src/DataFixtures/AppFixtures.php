<?php

namespace App\DataFixtures;

use App\Entity\RepresentantFamille;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

    }

    public function loadRepresentantsFamilles(ObjectManager $manager)
    {
        $rf = [
          ['id' => 1, 'login' => 'Thomas', 'motdepasse' => '123456', 'nom' => 'Mattone', 'prenom' => 'Thomas'
          , 'adresse' => 'Belfort', 'nomobile' => '0102030405', 'nofixe' => '0102030405', 'mail' => 'thomas@mattone'
          , 'datenaissance' => '2000-01-06', 'datefinadhesion' => '2019-12-02'],
          ['id' => 2, 'login' => 'Zoe', 'motdepasse' => '123456', 'nom' => 'Siena', 'prenom' => 'Zoe'
            , 'adresse' => 'Hyeres', 'nomobile' => '0102030405', 'nofixe' => '0102030405', 'mail' => 'zoe@siena'
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
            $manager->persist($new_rf);
            $manager->flush();
        }
    }

    public function loadMembresFamilles(ObjectManager $manager)
    {
        $if = [
          ['id' => 1, 'noClient' => 'A1', 'categorie' => 'Adulte', 'nom' => 'Mattone', 'prenom' => 'Thomas'
          , 'datenaissance' => '2000-01-06', 'tratiementdonnees' => 1, 'datemaj' => new \DateTime()
          , 'id_representantfamille' => 1, ],
          ['id' => 2, 'login' => 'Zoe', 'motdepasse' => '123456', 'nom' => 'Siena', 'prenom' => 'Zoe'
            , 'adresse' => 'Hyeres', 'nomobile' => '0102030405', 'nofixe' => '0102030405', 'mail' => 'zoe@siena'
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
            $manager->persist($new_rf);
            $manager->flush();
        }
    }
}
