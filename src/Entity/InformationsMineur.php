<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;
use phpDocumentor\Reflection\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

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
     * @ORM\OneToOne(targetEntity="App\Entity\MembreFamille", inversedBy="informationsMineur", cascade={"persist", "remove"})
     */
    private $membre_famille;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AutorisationSortie", mappedBy="informations_mineur")
     */
    private $autorisationSorties;

    /**
     * @ORM\Column(type="string")
     */
    private $ficheSanitaires;

    public function __construct()
    {
        $this->autorisationSorties = new ArrayCollection();
        $this->ficheSanitaires = new ArrayCollection();
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
        return $this->membre_famille;
    }

    public function setMembreFamille(?MembreFamille $membre_famille): self
    {
        $this->membre_famille = $membre_famille;

        return $this;
    }

    /**
     * @return Collection|AutorisationSortie[]
     */
    public function getAutorisationSorties(): Collection
    {
        return $this->autorisationSorties;
    }

    public function addAutorisationSorties(AutorisationSortie $autorisationSorty): self
    {
        if (!$this->autorisationSorties->contains($autorisationSorty)) {
            $this->autorisationSorties[] = $autorisationSorty;
            $autorisationSorty->setInformationsMineur($this);
        }

        return $this;
    }

    public function removeAutorisationSorties(AutorisationSortie $autorisationSorty): self
    {
        if ($this->autorisationSorties->contains($autorisationSorty)) {
            $this->autorisationSorties->removeElement($autorisationSorty);
            // set the owning side to null (unless already changed)
            if ($autorisationSorty->getInformationsMineur() === $this) {
                $autorisationSorty->setInformationsMineur(null);
            }
        }

        return $this;
    }


    public function getFicheSanitaires(): string
    {
        return $this->ficheSanitaires;
    }

    public function addFicheSanitaire(string $ficheSanitaire): self
    {
            $this->ficheSanitaires = $ficheSanitaire;


        return $this;
    }

    public function removeFicheSanitaire(FicheSanitaire $ficheSanitaire): self
    {
        if ($this->ficheSanitaires->contains($ficheSanitaire)) {
            $this->ficheSanitaires->removeElement($ficheSanitaire);
            $ficheSanitaire->removeInformationsMineur($this);
        }

        return $this;
    }

    public function addAutorisationSorty(AutorisationSortie $autorisationSorty): self
    {
        if (!$this->autorisationSorties->contains($autorisationSorty)) {
            $this->autorisationSorties[] = $autorisationSorty;
            $autorisationSorty->setInformationsMineur($this);
        }

        return $this;
    }

    public function removeAutorisationSorty(AutorisationSortie $autorisationSorty): self
    {
        if ($this->autorisationSorties->contains($autorisationSorty)) {
            $this->autorisationSorties->removeElement($autorisationSorty);
            // set the owning side to null (unless already changed)
            if ($autorisationSorty->getInformationsMineur() === $this) {
                $autorisationSorty->setInformationsMineur(null);
            }
        }

        return $this;
    }

    public function setFicheSanitaires(string $ficheSanitaires): self
    {
        $this->ficheSanitaires = $ficheSanitaires;

        return $this;
    }


}
