<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InformationResponsableLegalRepository")
 */
class InformationResponsableLegal
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
    private $profession;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MembreFamille", inversedBy="informationResponsableLegals")
     */
    private $membre_famille;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InformationEmployeur", mappedBy="informations_responsable_famille")
     */
    private $informationEmployeurs;

    public function __construct()
    {
        $this->informationEmployeurs = new ArrayCollection();
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getMembreFamille(): ?MembreFamille
    {
        return $this->membre_famille;
    }

    public function setMembreFamille(?MembreFamille $membre_famille): self
    {
        $this->membre_famille = $membre_famille;

        return $this;
    }

    /**
     * @return Collection|InformationEmployeur[]
     */
    public function getInformationEmployeurs(): Collection
    {
        return $this->informationEmployeurs;
    }

    public function addInformationEmployeur(InformationEmployeur $informationEmployeur): self
    {
        if (!$this->informationEmployeurs->contains($informationEmployeur)) {
            $this->informationEmployeurs[] = $informationEmployeur;
            $informationEmployeur->setInformationsResponsableFamille($this);
        }

        return $this;
    }

    public function removeInformationEmployeur(InformationEmployeur $informationEmployeur): self
    {
        if ($this->informationEmployeurs->contains($informationEmployeur)) {
            $this->informationEmployeurs->removeElement($informationEmployeur);
            // set the owning side to null (unless already changed)
            if ($informationEmployeur->getInformationsResponsableFamille() === $this) {
                $informationEmployeur->setInformationsResponsableFamille(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
