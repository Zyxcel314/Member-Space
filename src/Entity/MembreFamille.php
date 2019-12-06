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
     * @ORM\ManyToOne(targetEntity="App\Entity\RepresentantFamille", inversedBy="membreFamilles")
     */
    private $representant_famille;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InformationResponsableLegal", mappedBy="membre_famille")
     */
    private $informationResponsableLegals;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\InformationsMineur", mappedBy="membre_famille", cascade={"persist", "remove"})
     */
    private $informationsMineur;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\InformationMajeur", mappedBy="membre_famille", cascade={"persist", "remove"})
     */
    private $informationMajeur;

    public function __construct()
    {
        $this->id_representantFamille = new ArrayCollection();
        $this->informationResponsableLegals = new ArrayCollection();
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

    public function getRepresentantFamille(): ?RepresentantFamille
    {
        return $this->representant_famille;
    }

    public function setRepresentantFamille(?RepresentantFamille $representant_famille): self
    {
        $this->representant_famille = $representant_famille;

        return $this;
    }

    /**
     * @return Collection|InformationResponsableLegal[]
     */
    public function getInformationResponsableLegals(): Collection
    {
        return $this->informationResponsableLegals;
    }

    public function addInformationResponsableLegal(InformationResponsableLegal $informationResponsableLegal): self
    {
        if (!$this->informationResponsableLegals->contains($informationResponsableLegal)) {
            $this->informationResponsableLegals[] = $informationResponsableLegal;
            $informationResponsableLegal->setMembreFamille($this);
        }

        return $this;
    }

    public function removeInformationResponsableLegal(InformationResponsableLegal $informationResponsableLegal): self
    {
        if ($this->informationResponsableLegals->contains($informationResponsableLegal)) {
            $this->informationResponsableLegals->removeElement($informationResponsableLegal);
            // set the owning side to null (unless already changed)
            if ($informationResponsableLegal->getMembreFamille() === $this) {
                $informationResponsableLegal->setMembreFamille(null);
            }
        }

        return $this;
    }

    public function getInformationsMineur(): ?InformationsMineur
    {
        return $this->informationsMineur;
    }

    public function setInformationsMineur(?InformationsMineur $informationsMineur): self
    {
        $this->informationsMineur = $informationsMineur;

        // set (or unset) the owning side of the relation if necessary
        $newMembre_famille = $informationsMineur === null ? null : $this;
        if ($newMembre_famille !== $informationsMineur->getMembreFamille()) {
            $informationsMineur->setMembreFamille($newMembre_famille);
        }

        return $this;
    }

    public function getInformationMajeur(): ?InformationMajeur
    {
        return $this->informationMajeur;
    }

    public function setInformationMajeur(?InformationMajeur $informationMajeur): self
    {
        $this->informationMajeur = $informationMajeur;

        // set (or unset) the owning side of the relation if necessary
        $newMembre_famille = $informationMajeur === null ? null : $this;
        if ($newMembre_famille !== $informationMajeur->getMembreFamille()) {
            $informationMajeur->setMembreFamille($newMembre_famille);
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
