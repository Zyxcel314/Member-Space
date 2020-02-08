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
     * @ORM\OneToMany(targetEntity="App\Entity\Dispositions", mappedBy="droits")
     */
    private $dispositions;

    public function __construct()
    {
        $this->dispositions = new ArrayCollection();
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
     * @return Collection|Dispositions[]
     */
    public function getDispositions(): Collection
    {
        return $this->dispositions;
    }

    public function addDisposition(Dispositions $disposition): self
    {
        if (!$this->dispositions->contains($disposition)) {
            $this->dispositions[] = $disposition;
            $disposition->setDroits($this);
        }

        return $this;
    }

    public function removeDisposition(Dispositions $disposition): self
    {
        if ($this->dispositions->contains($disposition)) {
            $this->dispositions->removeElement($disposition);
            // set the owning side to null (unless already changed)
            if ($disposition->getDroits() === $this) {
                $disposition->setDroits(null);
            }
        }

        return $this;
    }
}
