<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FicheSanitaireRepository")
 */
class FicheSanitaire
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
     * @ORM\Column(type="string", length=10)
     */
    private $noTelephoneMedecinTraitant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $traitements;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $allergies;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pathologies;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $informationsComplementaires;

    /**
     * @ORM\Column(type="boolean")
     */
    private $formulaireQSSport;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\InformationsMineur", inversedBy="ficheSanitaires")
     */
    private $informations_mineur;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\CertificatMedical", mappedBy="fiche_sanitaire", cascade={"persist", "remove"})
     */
    private $certificatMedical;

    public function __construct()
    {
        $this->informations_mineur = new ArrayCollection();
    }

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

    public function getNoTelephoneMedecinTraitant(): ?string
    {
        return $this->noTelephoneMedecinTraitant;
    }

    public function setNoTelephoneMedecinTraitant(string $noTelephoneMedecinTraitant): self
    {
        $this->noTelephoneMedecinTraitant = $noTelephoneMedecinTraitant;

        return $this;
    }

    public function getTraitements(): ?string
    {
        return $this->traitements;
    }

    public function setTraitements(string $traitements): self
    {
        $this->traitements = $traitements;

        return $this;
    }

    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    public function setAllergies(string $allergies): self
    {
        $this->allergies = $allergies;

        return $this;
    }

    public function getPathologies(): ?string
    {
        return $this->pathologies;
    }

    public function setPathologies(string $pathologies): self
    {
        $this->pathologies = $pathologies;

        return $this;
    }

    public function getInformationsComplementaires(): ?string
    {
        return $this->informationsComplementaires;
    }

    public function setInformationsComplementaires(string $informationsComplementaires): self
    {
        $this->informationsComplementaires = $informationsComplementaires;

        return $this;
    }

    public function getFormulaireQSSport(): ?bool
    {
        return $this->formulaireQSSport;
    }

    public function setFormulaireQSSport(bool $formulaireQSSport): self
    {
        $this->formulaireQSSport = $formulaireQSSport;

        return $this;
    }

    /**
     * @return Collection|InformationsMineur[]
     */
    public function getInformationsMineur(): Collection
    {
        return $this->informations_mineur;
    }

    public function addInformationsMineur(InformationsMineur $informationsMineur): self
    {
        if (!$this->informations_mineur->contains($informationsMineur)) {
            $this->informations_mineur[] = $informationsMineur;
        }

        return $this;
    }

    public function removeInformationsMineur(InformationsMineur $informationsMineur): self
    {
        if ($this->informations_mineur->contains($informationsMineur)) {
            $this->informations_mineur->removeElement($informationsMineur);
        }

        return $this;
    }

    public function getCertificatMedical(): ?CertificatMedical
    {
        return $this->certificatMedical;
    }

    public function setCertificatMedical(?CertificatMedical $certificatMedical): self
    {
        $this->certificatMedical = $certificatMedical;

        // set (or unset) the owning side of the relation if necessary
        $newFiche_sanitaire = $certificatMedical === null ? null : $this;
        if ($newFiche_sanitaire !== $certificatMedical->getFicheSanitaire()) {
            $certificatMedical->setFicheSanitaire($newFiche_sanitaire);
        }

        return $this;
    }
}
