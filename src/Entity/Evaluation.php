<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

   

    #[ORM\ManyToOne(targetEntity: Apprenant::class, inversedBy: "evaluations")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Apprenant $apprenant = null;

    #[ORM\ManyToOne(targetEntity: Cours::class, inversedBy: "evaluations")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;

    #[ORM\Column(type: "integer")]
    private int $note; // Stocke les étoiles (1 à 5)

    

    public function getApprenant(): ?Apprenant { return $this->apprenant; }
    public function setApprenant(?Apprenant $apprenant): self {
        $this->apprenant = $apprenant;
        return $this;
    }

    public function getCours(): ?Cours { return $this->cours; }
    public function setCours(?Cours $cours): self {
        $this->cours = $cours;
        return $this;
    }

    public function getNote(): int { return $this->note; }
    public function setNote(int $note): self {
        $this->note = $note;
        return $this;
    }



    public function getId(): ?int
    {
        return $this->id;
    }



}