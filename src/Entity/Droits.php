<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DroitsRepository")
 */
class Droits
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
    private $code;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $libelle;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Gestionnaires", inversedBy="droits")
     */
    private $gestionnaire;

    public function __construct()
    {
        $this->gestionnaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|Gestionnaires[]
     */
    public function getGestionnaire(): Collection
    {
        return $this->gestionnaire;
    }

    public function addGestionnaire(Gestionnaires $gestionnaire): self
    {
        if (!$this->gestionnaire->contains($gestionnaire)) {
            $this->gestionnaire[] = $gestionnaire;
        }

        return $this;
    }

    public function removeGestionnaire(Gestionnaires $gestionnaire): self
    {
        if ($this->gestionnaire->contains($gestionnaire)) {
            $this->gestionnaire->removeElement($gestionnaire);
        }

        return $this;
    }
}
