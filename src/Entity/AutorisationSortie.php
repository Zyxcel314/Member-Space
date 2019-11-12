<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AutorisationSortieRepository")
 */
class AutorisationSortie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $idMineur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $flagDesactivation;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDesactivation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lien;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\InformationsMineur", inversedBy="id_autorisationSortie")
     */
    private $informationsMineur;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\InformationsMineur", cascade={"persist", "remove"})
     */
    private $id_informationsMineur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdMineur(): ?int
    {
        return $this->idMineur;
    }

    public function setIdMineur(int $idMineur): self
    {
        $this->idMineur = $idMineur;

        return $this;
    }

    public function getFlagDesactivation(): ?bool
    {
        return $this->flagDesactivation;
    }

    public function setFlagDesactivation(bool $flagDesactivation): self
    {
        $this->flagDesactivation = $flagDesactivation;

        return $this;
    }

    public function getDateDesactivation(): ?\DateTimeInterface
    {
        return $this->dateDesactivation;
    }

    public function setDateDesactivation(\DateTimeInterface $dateDesactivation): self
    {
        $this->dateDesactivation = $dateDesactivation;

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }

    public function getInformationsMineur(): ?InformationsMineur
    {
        return $this->informationsMineur;
    }

    public function setInformationsMineur(?InformationsMineur $informationsMineur): self
    {
        $this->informationsMineur = $informationsMineur;

        return $this;
    }

    public function getIdInformationsMineur(): ?InformationsMineur
    {
        return $this->id_informationsMineur;
    }

    public function setIdInformationsMineur(?InformationsMineur $id_informationsMineur): self
    {
        $this->id_informationsMineur = $id_informationsMineur;

        return $this;
    }
}
