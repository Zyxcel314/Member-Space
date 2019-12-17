<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GestionnairesRepository")
 */
class Gestionnaires implements UserInterface, \Serializable, EquatableInterface
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

    /**
     * @return array (Role|string)[]
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
        return ['ROLE_ADMIN'];
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    public function getPassword()
    {
        // TODO: Implement getPassword() method.
        return $this->getMotdepasse();
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
        return $this->getNom();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
        $this->setMotdepasse('');
    }


    public function isEqualTo(UserInterface $user)
    {
        //if ($this->password !== $user->getPassword()) {
        //    return false;
        //}

        //if ($this->salt !== $user->getSalt()) {
        //    return false;
        //}

        if ($this->nom !== $user->getNom()) {
            return false;
        }
        return true;
    }

    public function serialize()
    {
        //die('serialize');
        return serialize(array(
            $this->id,
            $this->nom,
            $this->motdepasse
        ));
    }

    public function unserialize( $serialized )
    {
        list (
            $this->id,
            $this->nom,
            $this->motdepasse
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }
}
