<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentaireRepository;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_commentaire = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $titre;

    #[ORM\Column(type: 'text')]
    private string $contenu;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $date_actuelle;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'commentaires')]
    #[ORM\JoinColumn(name: 'evenement_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Event $evenement = null;

    public function __construct()
    {
        $this->date_actuelle = new DateTimeImmutable();
    }

    public function getIdCommentaire(): ?int
    {
        return $this->id_commentaire;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getDateActuelle(): DateTimeImmutable
    {
        return $this->date_actuelle;
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
}
