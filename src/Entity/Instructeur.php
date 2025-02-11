<?php

namespace App\Entity;

use App\Repository\InstructeurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstructeurRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator', type: 'string')]
#[ORM\DiscriminatorMap(['user' => User::class, 'apprenant' => Apprenant::class, 'instructeur' => Instructeur::class])]
class Instructeur extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 100)]
    private string $nom;

    #[ORM\Column(type: "string", length: 100)]
    private string $prenom;

    #[ORM\Column(type: "string", length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $biographie = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\OneToMany(mappedBy: "instructeur", targetEntity: Cours::class)]
    private Collection $cours;

    public function __construct()
    {
        parent::__construct(); // Appelle le constructeur de User pour initialiser les propriétés communes
        $this->cours = new ArrayCollection();
    }

    // Getters & Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string
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

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(?string $biographie): self
    {
        $this->biographie = $biographie;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCours(Cours $cours): self
    {
        if (!$this->cours->contains($cours)) {
            $this->cours->add($cours);
            $cours->setInstructeur($this);
        }

        return $this;
    }

    public function removeCours(Cours $cours): self
    {
        if ($this->cours->removeElement($cours)) {
            if ($cours->getInstructeur() === $this) {
                $cours->setInstructeur(null);
            }
        }

        return $this;
    }
}