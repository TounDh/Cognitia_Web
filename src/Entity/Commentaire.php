<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text')]
    private ?string $contenu = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_actuelle = null;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'commentaires')]
    #[ORM\JoinColumn(name: 'evenement_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Event $evenement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getDateActuelle(): ?\DateTimeInterface
    {
        return $this->date_actuelle;
    }

    public function setDateActuelle(\DateTimeInterface $date_actuelle): self
    {
        $this->date_actuelle = $date_actuelle;
        return $this;
    }

    public function getEvenement(): ?Event
    {
        return $this->evenement;
    }

    public function setEvenement(?Event $evenement): self
    {
        $this->evenement = $evenement;
        return $this;
    }

    #[ORM\PrePersist]
    public function setDateActuelleValue(): void
    {
        if (is_null($this->date_actuelle)) {
            $this->date_actuelle = new \DateTime();
        }
    }
    
}
