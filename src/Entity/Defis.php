<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Defis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $titre;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "integer")]
    private int $pointsRecompense;

    #[ORM\Column(type: "string", length: 255)]
    private string $badgeRecompense;

    #[ORM\ManyToOne(targetEntity: Cours::class, inversedBy: "defis")]
    #[ORM\JoinColumn(name: 'cours_id', referencedColumnName: 'id')]
    private ?Cours $cours = null;

    // Getters and setters...
    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    public function getPointsRecompense(): int { return $this->pointsRecompense; }
    public function setPointsRecompense(int $pointsRecompense): self { $this->pointsRecompense = $pointsRecompense; return $this; }
    public function getBadgeRecompense(): string { return $this->badgeRecompense; }
    public function setBadgeRecompense(string $badgeRecompense): self { $this->badgeRecompense = $badgeRecompense; return $this; }
    public function getCours(): ?Cours { return $this->cours; }
    public function setCours(?Cours $cours): self { $this->cours = $cours; return $this; }
}
