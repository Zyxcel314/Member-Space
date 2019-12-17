<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InformationMajeurRepository")
 */
class InformationMajeur
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
    private $mail;

    /**
     * @ORM\Column(type="boolean")
     */
    private $communicationResponsableLegal;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MembreFamille", inversedBy="informationMajeur", cascade={"persist", "remove"})
     */
    private $membre_famille;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getCommunicationResponsableLegal(): ?bool
    {
        return $this->communicationResponsableLegal;
    }

    public function setCommunicationResponsableLegal(bool $communicationResponsableLegal): self
    {
        $this->communicationResponsableLegal = $communicationResponsableLegal;

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
}
