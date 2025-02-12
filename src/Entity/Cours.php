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
    private int $duree; // Durée du cours en minutes

    #[ORM\ManyToOne(targetEntity: Instructeur::class, inversedBy: "cours")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Instructeur $instructeur = null;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Quiz::class, cascade: ['persist', 'remove'])]
    private Collection $quizzes;

    #[ORM\OneToMany(mappedBy: "cours", targetEntity: Evaluation::class)]
    private Collection $evaluations;

    public function __construct()
    {
        $this->evaluations = new ArrayCollection();
        $this->datePublication = new \DateTime(); // Défaut à la date actuelle
        $this->quizzes = new ArrayCollection();

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

    public function getInstructeur(): ?Instructeur
    {
        return $this->instructeur;
    }

    public function setInstructeur(?Instructeur $instructeur): self
    {
        $this->instructeur = $instructeur;
        return $this;
    }

    public function getQuizzes(): Collection { return $this->quizzes; }


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
}
