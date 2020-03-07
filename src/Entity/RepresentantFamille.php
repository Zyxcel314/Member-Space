<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RepresentantFamilleRepository")
 */
class RepresentantFamille implements UserInterface, \Serializable, EquatableInterface
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
    private $login;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motdepasse;

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
    private $adresse;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $noMobile;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $noFixe;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $mail;

    /**
     * @ORM\Column(type="date")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateFinAdhesion;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estActive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InformationsFamille", mappedBy="representant_famille")
     */
    private $informationsFamilles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MembreFamille", mappedBy="representant_famille")
     */
    private $membreFamilles;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $mailTokenVerification;

    /**
     * @ORM\Column(type="integer")
     */
    private $codePostal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

    public function __construct()
    {
        $this->informationsFamilles = new ArrayCollection();
        $this->membreFamilles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNoMobile(): ?string
    {
        return $this->noMobile;
    }

    public function setNoMobile(string $noMobile): self
    {
        $this->noMobile = $noMobile;

        return $this;
    }

    public function getNoFixe(): ?string
    {
        return $this->noFixe;
    }

    public function setNoFixe(string $noFixe): self
    {
        $this->noFixe = $noFixe;

        return $this;
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

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getDateFinAdhesion(): ?\DateTimeInterface
    {
        return $this->dateFinAdhesion;
    }

    public function setDateFinAdhesion(\DateTimeInterface $dateFinAdhesion): self
    {
        $this->dateFinAdhesion = $dateFinAdhesion;

        return $this;
    }

    public function getEstActive(): ?bool
    {
        return $this->estActive;
    }

    public function setEstActive(bool $estActive): self
    {
        $this->estActive = $estActive;

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
        return ['ROLE_USER'];
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
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
        return $this->getLogin();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
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

        if ($this->login !== $user->getLogin()) {
            return false;
        }
        return true;
    }

    public function serialize()
    {
        //die('serialize');
        return serialize(array(
            $this->id,
            $this->login,
            $this->motdepasse
        ));
    }

    public function unserialize( $serialized )
    {
        list (
            $this->id,
            $this->login,
            $this->motdepasse
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @return Collection|InformationsFamille[]
     */
    public function getInformationsFamilles(): Collection
    {
        return $this->informationsFamilles;
    }

    public function addInformationsFamille(InformationsFamille $informationsFamille): self
    {
        if (!$this->informationsFamilles->contains($informationsFamille)) {
            $this->informationsFamilles[] = $informationsFamille;
            $informationsFamille->setRepresentantFamille($this);
        }

        return $this;
    }

    public function removeInformationsFamille(InformationsFamille $informationsFamille): self
    {
        if ($this->informationsFamilles->contains($informationsFamille)) {
            $this->informationsFamilles->removeElement($informationsFamille);
            // set the owning side to null (unless already changed)
            if ($informationsFamille->getRepresentantFamille() === $this) {
                $informationsFamille->setRepresentantFamille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MembreFamille[]
     */
    public function getMembreFamilles(): Collection
    {
        return $this->membreFamilles;
    }

    public function addMembreFamille(MembreFamille $membreFamille): self
    {
        if (!$this->membreFamilles->contains($membreFamille)) {
            $this->membreFamilles[] = $membreFamille;
            $membreFamille->setRepresentantFamille($this);
        }

        return $this;
    }

    public function removeMembreFamille(MembreFamille $membreFamille): self
    {
        if ($this->membreFamilles->contains($membreFamille)) {
            $this->membreFamilles->removeElement($membreFamille);
            // set the owning side to null (unless already changed)
            if ($membreFamille->getRepresentantFamille() === $this) {
                $membreFamille->setRepresentantFamille(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->login;
    }

    public function getMailTokenVerification(): ?string
    {
        return $this->mailTokenVerification;
    }

    public function setMailTokenVerification(string $mailTokenVerification): self
    {
        $this->mailTokenVerification = $mailTokenVerification;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }
}
