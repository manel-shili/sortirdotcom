<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 50)]
    #[Assert\NotBlank (message: "Merci de saisir un nom de sortie")]
    #[Assert\Length(
        max : 50,
        maxMessage : "Votre saisie ne doit pas dépasser {{limit}} caractères" )]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank (message: "Merci de saisir le nombre de places disponible")]
    private ?int $nbInscriptionMax = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message: "Un petit effort, 2 ou 3 mots suffise")]
    #[Assert\Length(
        max : 255,
        maxMessage : "Votre saisie ne doit pas dépasser {{ limit }} caractères" )]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    //#[Assert\DateTime( format : 'Y-m-d H:i')]
    #[Assert\Type("\DateTime")]
    private ?\DateTime $date_enregistrement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    //#[Assert\DateTime( format : 'Y-m-d H:i')]
    #[Assert\Type("\DateTime")]
    #[Assert\GreaterThan(
        propertyPath:'date_enregistrement',
        message: 'La date saisie ne peut pas être inférieure à {{ compared_value }}'
    )]
    private ?\DateTime $date_ouverture_inscription = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    //#[Assert\DateTime( format : 'Y-m-d H:i')]
    #[Assert\Type("\DateTime")]
    #[Assert\GreaterThan(
        propertyPath:'date_ouverture_inscription',
        message: 'La date saisie ne peut pas être inférieure à {{ compared_value }}'
    )]
    private ?\DateTime $date_fermeture_inscription = null;

    #[ORM\Column]
    private ?bool $isAnnulee = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    //#[Assert\DateTime( format : 'Y-m-d H:i')]
    #[Assert\Type("\DateTime")]
    #[Assert\GreaterThan(
        propertyPath:'date_fermeture_inscription',
        message: 'La date saisie ne peut pas être inférieure à {{ compared_value }}'
    )]
    private ?\DateTime $date_debut_sortie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    //#[Assert\DateTime( format : 'Y-m-d H:i')]
    #[Assert\Type("\DateTime")]
    #[Assert\GreaterThan(
        propertyPath:'date_debut_sortie',
        message:'La date saisie ne peut pas être inférieure à {{ compared_value }}'
    )]
    private ?\DateTime $date_fin_sortie = null;

    #[ORM\ManyToOne(inversedBy: 'sortie')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $organisateur = null;

    #[ORM\OneToMany(mappedBy: 'sortie', targetEntity: Inscription::class)]
    //TODO Change it to inscriptions
    private Collection $sortie;

    #[ORM\OneToMany(mappedBy: 'sortie', targetEntity: PhotoSortie::class)]
    private Collection $photoSortie;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lieu $adresse = null;

    //Field non mappé avec la base de données
    private ?string $etat;
    private ?int $nbInscrit;
    private ?bool $estInscrit;
    private ?\DateInterval $duree;

    /**
     * @return string|null
     */
    public function getEtat(): ?string
    {
        return $this->etat;
    }

    /**
     * @param string|null $etat
     */
    public function setEtat(?string $etat): void
    {
        $this->etat = $etat;
    }

    /**
     * @param \dateTime $dateTime
     */

    public function calculEtat(\dateTime $dateTime): void
    {
        if ( $this->getDateEnregistrement() <= $dateTime || $this->getDateOuvertureInscription() == null){ $this->setEtat('EN CREATION'); }
        if ( $this->getDateOuvertureInscription() !== null && $this->getDateOuvertureInscription() <= $dateTime){ $this->setEtat('OUVERT'); }
        if ( $this->getDateFermetureInscription() <= $dateTime){ $this->setEtat('FERME'); }
        if ( $this->getDateDebutSortie() <= $dateTime){ $this->setEtat('EN COURS'); }
        if ( $this->getDateFinSortie() <= $dateTime){ $this->setEtat('ARCHIVE'); }
    }

    /**
     * @return int|null
     */
    public function getNbInscrit(): ?int
    {
        return $this->nbInscrit;
    }

    /**
     * @param int|null $nbInscrit
     */
    public function setNbInscrit(?int $nbInscrit): void
    {
        $this->nbInscrit = $nbInscrit;
    }

    /**
     * @return bool|null
     */
    public function getEstInscrit(): ?bool
    {
        return $this->estInscrit;
    }

    /**
     * @param bool|null $estInscrit
     */
    public function setEstInscrit(?bool $estInscrit): void
    {
        $this->estInscrit = $estInscrit;
    }

    /**
     * @return bool|null
     */
    public function getDuree(): ?\DateInterval
    {
        return $this->duree;
    }

    /**
     * @param bool|null $duree
     */
    public function setDuree(?\DateInterval $duree): void
    {
        $this->duree = $duree;
    }

    public function __construct()
    {
        $this->sortie = new ArrayCollection();
        $this->photoSortie = new ArrayCollection();
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

    public function getNbInscriptionMax(): ?int
    {
        return $this->nbInscriptionMax;
    }

    public function setNbInscriptionMax(int $nbInscriptionMax): self
    {
        $this->nbInscriptionMax = $nbInscriptionMax;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateEnregistrement(): ?\DateTime
    {
        return $this->date_enregistrement;
    }

    public function setDateEnregistrement(\DateTime $date_enregistrement): self
    {
        $this->date_enregistrement = $date_enregistrement;

        return $this;
    }

    public function getDateOuvertureInscription(): ?\DateTime
    {
        return $this->date_ouverture_inscription;
    }

    public function setDateOuvertureInscription(\DateTime $date_ouverture_inscription=null): self
    {
        $this->date_ouverture_inscription = $date_ouverture_inscription;

        return $this;
    }

    public function getDateFermetureInscription(): ?\DateTime
    {
        return $this->date_fermeture_inscription;
    }

    public function setDateFermetureInscription(\DateTime $date_fermeture_inscription): self
    {
        $this->date_fermeture_inscription = $date_fermeture_inscription;

        return $this;
    }

    public function isIsAnnulee(): ?bool
    {
        return $this->isAnnulee;
    }

    public function setIsAnnulee(bool $isAnnulee): self
    {
        $this->isAnnulee = $isAnnulee;

        return $this;
    }

    public function getOrganisateur(): ?Utilisateur
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Utilisateur $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection<int, Inscription>
     */
    public function getSortie(): Collection
    {
        return $this->sortie;
    }

    public function addSortie(Inscription $sortie): self
    {
        if (!$this->sortie->contains($sortie)) {
            $this->sortie->add($sortie);
            $sortie->setSortie($this);
        }

        return $this;
    }

    public function removeSortie(Inscription $sortie): self
    {
        if ($this->sortie->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getSortie() === $this) {
                $sortie->setSortie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PhotoSortie>
     */
    public function getPhotoSortie(): Collection
    {
        return $this->photoSortie;
    }

    public function addPhotoSortie(PhotoSortie $photoSortie): self
    {
        if (!$this->photoSortie->contains($photoSortie)) {
            $this->photoSortie->add($photoSortie);
            $photoSortie->setSortie($this);
        }

        return $this;
    }

    public function removePhotoSortie(PhotoSortie $photoSortie): self
    {
        if ($this->photoSortie->removeElement($photoSortie)) {
            // set the owning side to null (unless already changed)
            if ($photoSortie->getSortie() === $this) {
                $photoSortie->setSortie(null);
            }
        }

        return $this;
    }

    public function getAdresse(): ?Lieu
    {
        return $this->adresse;
    }

    public function setAdresse(?Lieu $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDateDebutSortie(): ?\DateTime
    {
        return $this->date_debut_sortie;
    }

    public function setDateDebutSortie(\DateTime $date_debut_sortie): self
    {
        $this->date_debut_sortie = $date_debut_sortie;

        return $this;
    }

    public function getDateFinSortie(): ?\DateTime
    {
        return $this->date_fin_sortie;
    }

    public function setDateFinSortie(\DateTime $date_fin_sortie): self
    {
        $this->date_fin_sortie = $date_fin_sortie;

        return $this;
    }

}
