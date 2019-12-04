<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InformationEmployeurRepository")
 */
class InformationEmployeur
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $pays;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\InformationResponsableLegal", inversedBy="informationEmployeurs")
     */
    private $informations_responsable_famille;

    public function __construct()
    {
        $this->informations_responsable_famille = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * @return Collection|InformationResponsableLegal[]
     */
    public function getInformationsResponsableFamille(): Collection
    {
        return $this->informations_responsable_famille;
    }

    public function addInformationsResponsableFamille(InformationResponsableLegal $informationsResponsableFamille): self
    {
        if (!$this->informations_responsable_famille->contains($informationsResponsableFamille)) {
            $this->informations_responsable_famille[] = $informationsResponsableFamille;
        }

        return $this;
    }

    public function removeInformationsResponsableFamille(InformationResponsableLegal $informationsResponsableFamille): self
    {
        if ($this->informations_responsable_famille->contains($informationsResponsableFamille)) {
            $this->informations_responsable_famille->removeElement($informationsResponsableFamille);
        }

        return $this;
    }
}
