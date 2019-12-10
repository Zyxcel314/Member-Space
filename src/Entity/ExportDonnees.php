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
}
