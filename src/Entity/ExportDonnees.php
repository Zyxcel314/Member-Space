<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(type="datetime")
     */
    private $dateDernierExport;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\MembreFamille", inversedBy="exportDonnees")
     */
    private $membres_familles;

    public function __construct()
    {
        $this->membres_familles = new ArrayCollection();
    }

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

    /**
     * @return Collection|MembreFamille[]
     */
    public function getMembresFamilles(): Collection
    {
        return $this->membres_familles;
    }

    public function addMembresFamille(MembreFamille $membresFamille): self
    {
        if (!$this->membres_familles->contains($membresFamille)) {
            $this->membres_familles[] = $membresFamille;
        }

        return $this;
    }

    public function removeMembresFamille(MembreFamille $membresFamille): self
    {
        if ($this->membres_familles->contains($membresFamille)) {
            $this->membres_familles->removeElement($membresFamille);
        }

        return $this;
    }
}