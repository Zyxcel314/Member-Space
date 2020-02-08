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
    private $mail;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Dispositions", mappedBy="gestionnaire")
     */
    private $dispositions;

    public function __construct()
    {
        $this->idDroit = new ArrayCollection();
        $this->droits = new ArrayCollection();
        $this->dispositions = new ArrayCollection();
    }

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
     * @return array (Role|string)[]
     */
    public function getRoles()
    {
        // ON TOUCHE PLUS : CA MARCHE !
        // VERTSANDEN ?!
        if ( $this->getDispositions()->get(0)->getId() == 1 ){
            return ['ROLE_SUPER_ADMIN'];
        }
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
            $disposition->setGestionnaire($this);
        }

        return $this;
    }

    public function removeDisposition(Dispositions $disposition): self
    {
        if ($this->dispositions->contains($disposition)) {
            $this->dispositions->removeElement($disposition);
            // set the owning side to null (unless already changed)
            if ($disposition->getGestionnaire() === $this) {
                $disposition->setGestionnaire(null);
            }
        }

        return $this;
    }
}
