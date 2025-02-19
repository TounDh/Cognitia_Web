<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Modules
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $titre;

    #[ORM\Column(type: "text")]
    private string $contenu;

    #[ORM\Column(type: "integer")]
    private int $duree;

    #[ORM\ManyToOne(targetEntity: Cours::class, inversedBy: "modules")]
    #[ORM\JoinColumn(name: 'cours_id', referencedColumnName: 'id')]
    private ?Cours $cours = null;

    // Getters and setters...
    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }
    public function getContenu(): string { return $this->contenu; }
    public function setContenu(string $contenu): self { $this->contenu = $contenu; return $this; }
    public function getDuree(): int { return $this->duree; }
    public function setDuree(int $duree): self { $this->duree = $duree; return $this; }
    public function getCours(): ?Cours { return $this->cours; }
    public function setCours(?Cours $cours): self { $this->cours = $cours; return $this; }
}
