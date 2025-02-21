<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "Le contenu de la réponse ne peut pas être vide.")]
    private ?string $contenu = null;

    #[ORM\Column]
    private ?bool $estCorrecte = null;

    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'reponses')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Question $question = null;

    public function getId(): ?int { return $this->id; }

    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(string $contenu): self { $this->contenu = $contenu; return $this; }

    public function isEstCorrecte(): ?bool { return $this->estCorrecte; }
    public function setEstCorrecte(bool $estCorrecte): self { $this->estCorrecte = $estCorrecte; return $this; }

    public function getQuestion(): ?Question { return $this->question; }
    public function setQuestion(?Question $question): self { $this->question = $question; return $this; }

    public function __toString(): string { return $this->contenu; }
}
