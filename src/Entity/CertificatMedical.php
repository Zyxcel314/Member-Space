<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CertificatMedicalRepository")
 */
class CertificatMedical
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lien;

    /**
     * @ORM\Column(type="date")
     */
    private $dateEmission;

    /**
     * @ORM\Column(type="date")
     */
    private $dateValidite;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FicheSanitaire", inversedBy="certificatMedical", cascade={"persist", "remove"})
     */
    private $fiche_sanitaire;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateEmission(): ?\DateTimeInterface
    {
        return $this->dateEmission;
    }

    public function setDateEmission(\DateTimeInterface $dateEmission): self
    {
        $this->dateEmission = $dateEmission;

        return $this;
    }

    public function getDateValidite(): ?\DateTimeInterface
    {
        return $this->dateValidite;
    }

    public function setDateValidite(\DateTimeInterface $dateValidite): self
    {
        $this->dateValidite = $dateValidite;

        return $this;
    }

    public function getFicheSanitaire(): ?FicheSanitaire
    {
        return $this->fiche_sanitaire;
    }

    public function setFicheSanitaire(?FicheSanitaire $fiche_sanitaire): self
    {
        $this->fiche_sanitaire = $fiche_sanitaire;

        return $this;
    }
}
