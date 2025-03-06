<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Quiz;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
#[Vich\Uploadable]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'cours_images', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datePublication = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(length: 255)]
    private ?string $difficulte = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $instructeur = null;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Modules::class, orphanRemoval: true)]
    private Collection $modules;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Defis::class, orphanRemoval: true)]
    private Collection $defis;


    #[ORM\OneToMany(mappedBy: "cours", targetEntity: Evaluation::class)]
    private Collection $evaluations;

    #[ORM\OneToOne(targetEntity: Quiz::class, mappedBy: "cours", cascade: ["persist", "remove"])]
    private $quiz;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->defis = new ArrayCollection();
        $this->datePublication = new \DateTime();
        $this->evaluations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
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

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(?\DateTimeInterface $datePublication): self
    {
        $this->datePublication = $datePublication;
        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;
        return $this;
    }

    public function getDifficulte(): ?string
    {
        return $this->difficulte;
    }

    public function setDifficulte(?string $difficulte): self
    {
        $this->difficulte = $difficulte;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
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

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        // unset the owning side of the relation if necessary
        if ($quiz === null && $this->quiz !== null) {
            $this->quiz->setCours($this);
        }

        // set the owning side of the relation if necessary
        if ($quiz !== null && $quiz->getCours() !== $this) {
            $quiz->setCours($this);
        }

        $this->quiz = $quiz;

        return $this;
    }
}
