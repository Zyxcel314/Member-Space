<?php

namespace App\Entity;

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
     * @ORM\OneToOne(targetEntity="App\Entity\InformationsMineur", cascade={"persist", "remove"})
     */
    private $id_informationMineur;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\CertificatMedical", cascade={"persist", "remove"})
     */
    private $id_certificatMedical;

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

    public function getIdInformationMineur(): ?InformationsMineur
    {
        return $this->id_informationMineur;
    }

    public function setIdInformationMineur(?InformationsMineur $id_informationMineur): self
    {
        $this->id_informationMineur = $id_informationMineur;

        return $this;
    }

    public function getIdCertificatMedical(): ?CertificatMedical
    {
        return $this->id_certificatMedical;
    }

    public function setIdCertificatMedical(?CertificatMedical $id_certificatMedical): self
    {
        $this->id_certificatMedical = $id_certificatMedical;

        return $this;
    }
}
