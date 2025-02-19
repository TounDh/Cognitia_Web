<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $titre;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $datePublication;

    #[ORM\Column(type: "integer")]
    private int $duree; // Duration of the course in minutes

    #[ORM\Column(type: "string", length: 255)]
    private string $difficulte; // Difficulty level (now treated as a string)

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $prix; // Price of the course

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "cours")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $instructeur = null; // Changed from Instructeur to User

    #[ORM\OneToMany(mappedBy: "cours", targetEntity: Evaluation::class)]
    private Collection $evaluations;


    #[ORM\ManyToOne(targetEntity: Panier::class, inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Panier $panier = null;


    #[ORM\OneToMany(mappedBy: "cours", targetEntity: Modules::class)]
    private Collection $modules;

    #[ORM\OneToMany(mappedBy: "cours", targetEntity: Defis::class)]
    private Collection $defis;


    public function __construct()
    {
        $this->evaluations = new ArrayCollection();
        $this->datePublication = new \DateTime(); // Défaut à la date actuelle
        $this->modules = new ArrayCollection();
        $this->defis = new ArrayCollection();
        $this->datePublication = new \DateTime(); // Default to the current date
    }

    // Getters & Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getDatePublication(): \DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeInterface $datePublication): self
    {
        $this->datePublication = $datePublication;
        return $this;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;
        return $this;
    }

    public function getDifficulte(): string
    {
        return $this->difficulte;
    }

    public function setDifficulte(string $difficulte): self
    {
        // Validate the value to ensure it's one of the accepted values
        $validDifficulties = ['Beginner', 'Intermediate', 'Advanced'];
        if (!in_array($difficulte, $validDifficulties)) {
            throw new \InvalidArgumentException('Invalid difficulty level.');
        }
        $this->difficulte = $difficulte;
        return $this;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getInstructeur(): ?User
    {
        return $this->instructeur;
    }

    public function setInstructeur(?User $instructeur): self
    {
        $this->instructeur = $instructeur;
        return $this;
    }

    /**
     * @return Collection<int, Evaluation>
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations->add($evaluation);
            $evaluation->setCours($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            if ($evaluation->getCours() === $this) {
                $evaluation->setCours(null);
            }
        }

        return $this;
    }


    public function getPanier(): ?Panier
{
    return $this->panier;
}

public function setPanier(?Panier $panier): self
{
    $this->panier = $panier;

    return $this;
}


    /**
     * @return Collection<int, Modules>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Modules $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
            $module->setCours($this);
        }

        return $this;
    }

    public function removeModule(Modules $module): self
    {
        if ($this->modules->removeElement($module)) {
            if ($module->getCours() === $this) {
                $module->setCours(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Defis>
     */
    public function getDefis(): Collection
    {
        return $this->defis;
    }

    public function addDefis(Defis $defis): self
    {
        if (!$this->defis->contains($defis)) {
            $this->defis->add($defis);
            $defis->setCours($this);
        }

        return $this;
    }

    public function removeDefis(Defis $defis): self
    {
        if ($this->defis->removeElement($defis)) {
            if ($defis->getCours() === $this) {
                $defis->setCours(null);
            }
        }

        return $this;
    }
}
