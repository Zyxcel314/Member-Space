<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InformationsFamilleRepository")
 */
class InformationsFamille
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $dateModification;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $noAllocataire;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nomCAF;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbEnfants;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estMonoparentale;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $regimeProtectionSociale;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RepresentantFamille", inversedBy="informationsFamilles")
     */
    private $representant_famille;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTimeInterface $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getNoAllocataire(): ?string
    {
        return $this->noAllocataire;
    }

    public function setNoAllocataire(string $noAllocataire): self
    {
        $this->noAllocataire = $noAllocataire;

        return $this;
    }

    public function getNomCAF(): ?string
    {
        return $this->nomCAF;
    }

    public function setNomCAF(string $nomCAF): self
    {
        $this->nomCAF = $nomCAF;

        return $this;
    }

    public function getNbEnfants(): ?int
    {
        return $this->nbEnfants;
    }

    public function setNbEnfants(int $nbEnfants): self
    {
        $this->nbEnfants = $nbEnfants;

        return $this;
    }

    public function getEstMonoparentale(): ?bool
    {
        return $this->estMonoparentale;
    }

    public function setEstMonoparentale(bool $estMonoparentale): self
    {
        $this->estMonoparentale = $estMonoparentale;

        return $this;
    }

    public function getRegimeProtectionSociale(): ?string
    {
        return $this->regimeProtectionSociale;
    }

    public function setRegimeProtectionSociale(string $regimeProtectionSociale): self
    {
        $this->regimeProtectionSociale = $regimeProtectionSociale;

        return $this;
    }

    public function getRepresentantFamille(): ?RepresentantFamille
    {
        return $this->representant_famille;
    }

    public function setRepresentantFamille(?RepresentantFamille $representant_famille): self
    {
        $this->representant_famille = $representant_famille;

        return $this;
    }
}
