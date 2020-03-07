<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/*
    Pourquoi une entité "Dispositions" ?
    Un gestionnaire peut disposer de plusieurs droits,
    et un droit précis peut être possédé par plusieurs gestionnaires ;
    sous Symfony cela prendra la forme d'une relation ManyToMany (comme c'était le cas avant).
    Sauf que ce type de relation crée automatiquement une table intermédiaire avec comme attributs
    les ids du gestionnnaire et du droit. c'est pour cela que j'ai décomposé ce ManyToMany en deux ManyToOne
    car c'est plus simple à manipuler.
*/
/**
 * @ORM\Entity(repositoryClass="App\Repository\DispositionsRepository")
 */
class Dispositions
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gestionnaires", inversedBy="dispositions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gestionnaire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Droits", inversedBy="dispositions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $droits;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGestionnaire(): ?Gestionnaires
    {
        return $this->gestionnaire;
    }

    public function setGestionnaire(?Gestionnaires $gestionnaire): self
    {
        $this->gestionnaire = $gestionnaire;

        return $this;
    }

    public function getDroits(): ?Droits
    {
        return $this->droits;
    }

    public function setDroits(?Droits $droits): self
    {
        $this->droits = $droits;

        return $this;
    }
}
