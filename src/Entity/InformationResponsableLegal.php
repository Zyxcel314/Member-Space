<?php

namespace App\Entity;

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
     * @ORM\OneToOne(targetEntity="App\Entity\MembreFamille", mappedBy="id_informationResponsableLegal", cascade={"persist", "remove"})
     */
    private $membreFamille;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\InformationEmployeur", cascade={"persist", "remove"})
     */
    private $id_informationEmployeur;

    public function getId(): ?int
    {
        return $this->id;
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
        return $this->membreFamille;
    }

    public function setMembreFamille(?MembreFamille $membreFamille): self
    {
        $this->membreFamille = $membreFamille;

        // set (or unset) the owning side of the relation if necessary
        $newId_informationResponsableLegal = $membreFamille === null ? null : $this;
        if ($newId_informationResponsableLegal !== $membreFamille->getIdInformationResponsableLegal()) {
            $membreFamille->setIdInformationResponsableLegal($newId_informationResponsableLegal);
        }

        return $this;
    }

    public function getIdInformationEmployeur(): ?InformationEmployeur
    {
        return $this->id_informationEmployeur;
    }

    public function setIdInformationEmployeur(?InformationEmployeur $id_informationEmployeur): self
    {
        $this->id_informationEmployeur = $id_informationEmployeur;

        return $this;
    }
}
