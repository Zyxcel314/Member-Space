<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MembreFamilleRepository")
 */
class MembreFamille
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
    private $noClient;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;

    /**
     * @ORM\Column(type="date")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="boolean")
     */
    private $traitementDonnees;

    /**
     * @ORM\Column(type="date")
     */
    private $dateMAJ;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RepresentantFamille", mappedBy="membreFamille")
     */
    private $id_representantFamille;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ExportDonnees", inversedBy="membreFamille", cascade={"persist", "remove"})
     */
    private $id_dernierExport;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ExportDonnees", mappedBy="id_membreFamille", cascade={"persist", "remove"})
     */
    private $exportDonnees;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\InformationsMineur", inversedBy="membreFamille", cascade={"persist", "remove"})
     */
    private $id_informationMineur;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\InformationMajeur", inversedBy="membreFamille", cascade={"persist", "remove"})
     */
    private $idÃ_informationMajeur;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\InformationResponsableLegal", inversedBy="membreFamille", cascade={"persist", "remove"})
     */
    private $id_informationResponsableLegal;

    public function __construct()
    {
        $this->id_representantFamille = new ArrayCollection();
    }


    public function getNoClient(): ?string
    {
        return $this->noClient;
    }

    public function setNoClient(string $noClient): self
    {
        $this->noClient = $noClient;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

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

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getTraitementDonnees(): ?bool
    {
        return $this->traitementDonnees;
    }

    public function setTraitementDonnees(bool $traitementDonnees): self
    {
        $this->traitementDonnees = $traitementDonnees;

        return $this;
    }

    public function getDateMAJ(): ?\DateTimeInterface
    {
        return $this->dateMAJ;
    }

    public function setDateMAJ(\DateTimeInterface $dateMAJ): self
    {
        $this->dateMAJ = $dateMAJ;

        return $this;
    }

    /**
     * @return Collection|RepresentantFamille[]
     */
    public function getIdRepresentantFamille(): Collection
    {
        return $this->id_representantFamille;
    }

    public function addIdRepresentantFamille(RepresentantFamille $idRepresentantFamille): self
    {
        if (!$this->id_representantFamille->contains($idRepresentantFamille)) {
            $this->id_representantFamille[] = $idRepresentantFamille;
            $idRepresentantFamille->setMembreFamille($this);
        }

        return $this;
    }

    public function removeIdRepresentantFamille(RepresentantFamille $idRepresentantFamille): self
    {
        if ($this->id_representantFamille->contains($idRepresentantFamille)) {
            $this->id_representantFamille->removeElement($idRepresentantFamille);
            // set the owning side to null (unless already changed)
            if ($idRepresentantFamille->getMembreFamille() === $this) {
                $idRepresentantFamille->setMembreFamille(null);
            }
        }

        return $this;
    }

    public function getIdDernierExport(): ?ExportDonnees
    {
        return $this->id_dernierExport;
    }

    public function setIdDernierExport(?ExportDonnees $id_dernierExport): self
    {
        $this->id_dernierExport = $id_dernierExport;

        return $this;
    }

    public function getExportDonnees(): ?ExportDonnees
    {
        return $this->exportDonnees;
    }

    public function setExportDonnees(ExportDonnees $exportDonnees): self
    {
        $this->exportDonnees = $exportDonnees;

        // set the owning side of the relation if necessary
        if ($this !== $exportDonnees->getIdMembreFamille()) {
            $exportDonnees->setIdMembreFamille($this);
        }

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

    public function getIdÃInformationMajeur(): ?InformationMajeur
    {
        return $this->idÃ_informationMajeur;
    }

    public function setIdÃInformationMajeur(?InformationMajeur $idÃ_informationMajeur): self
    {
        $this->idÃ_informationMajeur = $idÃ_informationMajeur;

        return $this;
    }

    public function getIdInformationResponsableLegal(): ?InformationResponsableLegal
    {
        return $this->id_informationResponsableLegal;
    }

    public function setIdInformationResponsableLegal(?InformationResponsableLegal $id_informationResponsableLegal): self
    {
        $this->id_informationResponsableLegal = $id_informationResponsableLegal;

        return $this;
    }

}
