<?php

namespace App\Entity;

use App\Repository\ResultatRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ResultatRepository::class)]
class Resultat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: "resultats")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Le résultat doit être associé à un apprenant")]
    private ?User $apprenant = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le score est obligatoire")]
    private ?int $score = null;

    public function getId(): ?int { return $this->id; }

    public function getQuiz(): ?Quiz { return $this->quiz; }
    public function setQuiz(?Quiz $quiz): self { $this->quiz = $quiz; return $this; }

    public function getApprenant(): ?User { return $this->apprenant; }
    public function setApprenant(?User $apprenant): self { $this->apprenant = $apprenant; return $this; }

    public function getScore(): ?int { return $this->score; }
    public function setScore(int $score): self { $this->score = $score; return $this; }

    public function __toString(): string { return "Résultat de " . $this->apprenant->getUserIdentifier() . " - Score : " . $this->score; }
}
