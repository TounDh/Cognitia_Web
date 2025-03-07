<?php

namespace App\Entity;

use App\Repository\CertificatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificatRepository::class)]
class Certificat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'certificats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $apprenant = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'certificats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: 'float')]
    private ?float $score = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateObtention = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApprenant(): ?User { return $this->apprenant; }
    public function setApprenant(?User $apprenant): self { $this->apprenant = $apprenant; return $this; }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getDateObtention(): ?\DateTimeImmutable
    {
        return $this->dateObtention;
    }

    public function setDateObtention(\DateTimeImmutable $dateObtention): self
    {
        $this->dateObtention = $dateObtention;
        return $this;
    }

}
