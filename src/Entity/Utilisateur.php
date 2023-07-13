<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]

#[UniqueEntity(fields: ['username'], message: 'Le {{ label }} saisi est déjà utilisé ')]
#[UniqueEntity(fields:['courriel'], message: 'Le {{ label }} saisie est déjà utilisé')]

class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\Length(
        min : 1,
        max : 20,
        minMessage : 'Votre saisie doit être minimum de {{ limit }} caractères',
        maxMessage : 'Votre saisie ne doit pas dépasser {{ limit }} caractères' )]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Assert\Email(
        message : 'L`adresse email saisie {{ value }}  n`est pas valide' )]
    private ?string $courriel = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 20)]
    #[Assert\Length(
        min : 1,
        max : 20,
        minMessage : 'Votre saisie doit être minimum de {{ limit }} caractères ',
        maxMessage : 'Votre saisie ne doit pas dépasser {{ limit }} caractères' )]
    private ?string $nom = null;

    #[ORM\Column(length: 20)]
    #[Assert\Length(
        min : 1,
        max : 20,
        minMessage : 'Votre saisie doit être minimum de {{ limit }} caractères' ,
        maxMessage : 'Votre saisie ne doit pas dépasser {{ limit }} caractères' )]
    private ?string $prenom = null;

    #[Assert\Regex(
        pattern: '/^[0-9]{10}$/',
        message: 'Votre saisie n`est pas valide',
        match: true,
    )]
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column]
    private ?bool $isActif = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $nomPhoto = null;

    #[ORM\Column]
    private ?bool $isCguAccepte = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?Site $site = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Inscription::class)]
    private Collection $inscription;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class)]
    private Collection $sortie;

    public function __construct()
    {
        $this->inscription = new ArrayCollection();
        $this->sortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCourriel(): ?string
    {
        return $this->courriel;
    }

    public function setCourriel(string $courriel): self
    {
        $this->courriel = $courriel;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function isIsActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(bool $isActif): self
    {
        $this->isActif = $isActif;

        return $this;
    }

    public function getNomPhoto(): ?string
    {
        return $this->nomPhoto;
    }

    public function setNomPhoto(?string $nomPhoto): self
    {
        $this->nomPhoto = $nomPhoto;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection<int, Inscription>
     */
    public function getInscription(): Collection
    {
        return $this->inscription;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscription->contains($inscription)) {
            $this->inscription->add($inscription);
            $inscription->setUtilisateur($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscription->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getUtilisateur() === $this) {
                $inscription->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortie(): Collection
    {
        return $this->sortie;
    }

    public function addSortie(Sortie $sortie): self
    {
        if (!$this->sortie->contains($sortie)) {
            $this->sortie->add($sortie);
            $sortie->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        if ($this->sortie->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getOrganisateur() === $this) {
                $sortie->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function isIsCguAccepte(): ?bool
    {
        return $this->isCguAccepte;
    }

    public function setIsCguAccepte(bool $isCguAccepte): self
    {
        $this->isCguAccepte = $isCguAccepte;

        return $this;
    }
}