<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GestionnairesRepository")
 */
class Gestionnaires
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
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motdepasse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $idGoogleAuth;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Droits", mappedBy="gestionnaire")
     */
    private $droits;

    public function __construct()
    {
        $this->idDroit = new ArrayCollection();
        $this->droits = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMotdepasse(): ?string
    {
        return $this->motdepasse;
    }

    public function setMotdepasse(string $motdepasse): self
    {
        $this->motdepasse = $motdepasse;

        return $this;
    }

    public function getIdGoogleAuth(): ?string
    {
        return $this->idGoogleAuth;
    }

    public function setIdGoogleAuth(?string $idGoogleAuth): self
    {
        $this->idGoogleAuth = $idGoogleAuth;

        return $this;
    }

    /**
     * @return Collection|Droits[]
     */
    public function getDroits(): Collection
    {
        return $this->droits;
    }

    public function addDroit(Droits $droit): self
    {
        if (!$this->droits->contains($droit)) {
            $this->droits[] = $droit;
            $droit->addGestionnaire($this);
        }

        return $this;
    }

    public function removeDroit(Droits $droit): self
    {
        if ($this->droits->contains($droit)) {
            $this->droits->removeElement($droit);
            $droit->removeGestionnaire($this);
        }

        return $this;
    }
}
