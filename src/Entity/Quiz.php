<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Cours;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le champ titre est obligatoire")]
    private ?string $titre = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotBlank(message: "Le champ temps est obligatoire")]
    private ?int $tempsMax = null;

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: Question::class, cascade: ['persist', 'remove'])]
    private Collection $questions;

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: Resultat::class, cascade: ['remove'])]
    private Collection $resultats;

    #[ORM\OneToOne(inversedBy: "quiz", targetEntity: Cours::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Le quiz doit être associé à un cours")]
    private $cours;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Le quiz doit avoir un instructeur")]
    private ?User $instructeur = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $apprenant = null;

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: Certificat::class, cascade: ['remove'])]
    private Collection $certificats;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->resultats = new ArrayCollection();
        $this->certificats = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }
    
    public function getQuestions(): Collection { return $this->questions; }
    
    public function getTempsMax(): ?int { return $this->tempsMax; }
    public function setTempsMax(?int $tempsMax): self { $this->tempsMax = $tempsMax; return $this; }

    public function getResultats(): Collection { return $this->resultats; }

    public function getCours(): ?Cours { return $this->cours; }
    public function setCours(Cours $cours): self
    {
        if ($cours->getQuiz() !== null && $cours->getQuiz() !== $this) {
            throw new \Exception('Un cours ne peut avoir qu\'un seul quiz');
        }

        $this->cours = $cours;

        return $this;
    }
    public function getInstructeur(): ?User { return $this->instructeur; }
    public function setInstructeur(?User $instructeur): self { $this->instructeur = $instructeur; return $this; }
    public function getApprenant(): ?User { return $this->apprenant; }
    public function setApprenant(?User $apprenant): self { $this->apprenant = $apprenant; return $this; }
    public function __toString(): string { return $this->titre; }
    
    /**
     * @return Collection<int, Certificat>
     */
    public function getCertificats(): Collection
    {
        return $this->certificats;
    }

    public function addCertificat(Certificat $certificat): self
    {
        if (!$this->certificats->contains($certificat)) {
            $this->certificats[] = $certificat;
            $certificat->setQuiz($this);
        }
        return $this;
    }

    public function removeCertificat(Certificat $certificat): self
    {
        if ($this->certificats->removeElement($certificat)) {
            if ($certificat->getQuiz() === $this) {
                $certificat->setQuiz(null);
            }
        }
        return $this;
    }

}

