<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExportDonneesRepository")
 */
class ExportDonnees
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
    private $dateDernierExport;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MembreFamille", mappedBy="id_dernierExport", cascade={"persist", "remove"})
     */
    private $membreFamille;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MembreFamille", inversedBy="exportDonnees", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_membreFamille;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDernierExport(): ?\DateTimeInterface
    {
        return $this->dateDernierExport;
    }

    public function setDateDernierExport(\DateTimeInterface $dateDernierExport): self
    {
        $this->dateDernierExport = $dateDernierExport;

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
        $newId_dernierExport = $membreFamille === null ? null : $this;
        if ($newId_dernierExport !== $membreFamille->getIdDernierExport()) {
            $membreFamille->setIdDernierExport($newId_dernierExport);
        }

        return $this;
    }

    public function getIdMembreFamille(): ?MembreFamille
    {
        return $this->id_membreFamille;
    }

    public function setIdMembreFamille(MembreFamille $id_membreFamille): self
    {
        $this->id_membreFamille = $id_membreFamille;

        return $this;
    }
}
