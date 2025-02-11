<?php

namespace App\Entity;

use App\Repository\ApprenantRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ApprenantRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator', type: 'string')]
#[ORM\DiscriminatorMap(['user' => User::class, 'apprenant' => Apprenant::class, 'instructeur' => Instructeur::class])]
class Apprenant extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $Level = null;

    #[ORM\Column(type: 'string')]
    private $nom;

    #[ORM\Column(type: 'string')]
    private $prenom;

    #[ORM\Column(type: 'string')]
    private $telephone;

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $interests = []; 

    #[ORM\ManyToMany(targetEntity: Cours::class, inversedBy: "apprenant")]
    #[ORM\JoinTable(name: "student_cours")]
    private Collection $cours;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $completedCourses = 0;

    #[ORM\OneToMany(mappedBy: "apprenant", targetEntity: Evaluation::class)]
    private Collection $evaluations;

    public function __construct()
    {
        parent::__construct();
        $this->cours = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
    }

    // Getters & Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?string
    {
        return $this->Level;
    }

    public function setLevel(?string $Level): self
    {
        $this->Level = $Level;
        return $this;
    }

    public function getInterests(): ?array
    {
        return $this->interests;
    }

    public function setInterests(?array $interests): self
    {
        $this->interests = $interests;
        return $this;
    }

    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCours(Cours $cours): self
    {
        if (!$this->cours->contains($cours)) {
            $this->cours[] = $cours;
        }
        return $this;
    }

    public function removeCours(Cours $cours): self
    {
        $this->cours->removeElement($cours);
        return $this;
    }

    public function getCompletedCourses(): ?int
    {
        return $this->completedCourses;
    }

    public function setCompletedCourses(?int $completedCourses): self
    {
        $this->completedCourses = $completedCourses;
        return $this;
    }

    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
        }
        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        $this->evaluations->removeElement($evaluation);
        return $this;
    }
}