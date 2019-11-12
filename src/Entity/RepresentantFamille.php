<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RepresentantFamilleRepository")
 */
class RepresentantFamille
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motdepasse;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $noMobile;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $noFixe;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $mail;

    /**
     * @ORM\Column(type="date")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="date")
     */
    private $dateFinAdhesion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MembreFamille", inversedBy="id_representantFamille")
     */
    private $membreFamille;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\InformationsFamille", cascade={"persist", "remove"})
     */
    private $id_informationsFamille;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getMotdepasse(): ?string
    {
        return $this->motdepasse;
    }

    public function setMotdepasse(string $motdepasse): self
    {
        $this->motdepasse = $motdepasse;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNoMobile(): ?string
    {
        return $this->noMobile;
    }

    public function setNoMobile(string $noMobile): self
    {
        $this->noMobile = $noMobile;

        return $this;
    }

    public function getNoFixe(): ?string
    {
        return $this->noFixe;
    }

    public function setNoFixe(string $noFixe): self
    {
        $this->noFixe = $noFixe;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getDateFinAdhesion(): ?\DateTimeInterface
    {
        return $this->dateFinAdhesion;
    }

    public function setDateFinAdhesion(\DateTimeInterface $dateFinAdhesion): self
    {
        $this->dateFinAdhesion = $dateFinAdhesion;

        return $this;
    }

    public function getMembreFamille(): ?MembreFamille
    {
        return $this->membreFamille;
    }

    public function setMembreFamille(?MembreFamille $membreFamille): self
    {
        $this->membreFamille = $membreFamille;

        return $this;
    }

    public function getIdInformationsFamille(): ?InformationsFamille
    {
        return $this->id_informationsFamille;
    }

    public function setIdInformationsFamille(?InformationsFamille $id_informationsFamille): self
    {
        $this->id_informationsFamille = $id_informationsFamille;

        return $this;
    }
}
