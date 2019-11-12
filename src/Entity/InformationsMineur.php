<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InformationsMineurRepository")
 */
class InformationsMineur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $autorisationTransport;

    /**
     * @ORM\Column(type="boolean")
     */
    private $droitImage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $autorisationTransportMedical;

    /**
     * @ORM\Column(type="boolean")
     */
    private $autorisationSortieSeul;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MembreFamille", mappedBy="id_informationMineur", cascade={"persist", "remove"})
     */
    private $membreFamille;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AutorisationSortie", mappedBy="informationsMineur")
     */
    private $id_autorisationSortie;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FicheSanitaire", cascade={"persist", "remove"})
     */
    private $id_ficheSanitaire;

    public function __construct()
    {
        $this->id_autorisationSortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAutorisationTransport(): ?bool
    {
        return $this->autorisationTransport;
    }

    public function setAutorisationTransport(bool $autorisationTransport): self
    {
        $this->autorisationTransport = $autorisationTransport;

        return $this;
    }

    public function getDroitImage(): ?bool
    {
        return $this->droitImage;
    }

    public function setDroitImage(bool $droitImage): self
    {
        $this->droitImage = $droitImage;

        return $this;
    }

    public function getAutorisationTransportMedical(): ?bool
    {
        return $this->autorisationTransportMedical;
    }

    public function setAutorisationTransportMedical(bool $autorisationTransportMedical): self
    {
        $this->autorisationTransportMedical = $autorisationTransportMedical;

        return $this;
    }

    public function getAutorisationSortieSeul(): ?bool
    {
        return $this->autorisationSortieSeul;
    }

    public function setAutorisationSortieSeul(bool $autorisationSortieSeul): self
    {
        $this->autorisationSortieSeul = $autorisationSortieSeul;

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
        $newId_informationMineur = $membreFamille === null ? null : $this;
        if ($newId_informationMineur !== $membreFamille->getIdInformationMineur()) {
            $membreFamille->setIdInformationMineur($newId_informationMineur);
        }

        return $this;
    }

    /**
     * @return Collection|AutorisationSortie[]
     */
    public function getIdAutorisationSortie(): Collection
    {
        return $this->id_autorisationSortie;
    }

    public function addIdAutorisationSortie(AutorisationSortie $idAutorisationSortie): self
    {
        if (!$this->id_autorisationSortie->contains($idAutorisationSortie)) {
            $this->id_autorisationSortie[] = $idAutorisationSortie;
            $idAutorisationSortie->setInformationsMineur($this);
        }

        return $this;
    }

    public function removeIdAutorisationSortie(AutorisationSortie $idAutorisationSortie): self
    {
        if ($this->id_autorisationSortie->contains($idAutorisationSortie)) {
            $this->id_autorisationSortie->removeElement($idAutorisationSortie);
            // set the owning side to null (unless already changed)
            if ($idAutorisationSortie->getInformationsMineur() === $this) {
                $idAutorisationSortie->setInformationsMineur(null);
            }
        }

        return $this;
    }

    public function getIdFicheSanitaire(): ?FicheSanitaire
    {
        return $this->id_ficheSanitaire;
    }

    public function setIdFicheSanitaire(?FicheSanitaire $id_ficheSanitaire): self
    {
        $this->id_ficheSanitaire = $id_ficheSanitaire;

        return $this;
    }
}
