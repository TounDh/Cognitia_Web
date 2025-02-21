<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;



#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le titre est requis.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le titre doit contenir au moins 3 caractères.")]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: "La description est requise.")]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message: "La date de début est requise.")]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message: "La date de fin est requise.")]
    #[Assert\GreaterThan(propertyPath: "date_debut", message: "La date de fin doit être après la date de début.")]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le lieu est requis.")]
    private ?string $lieu = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Assert\NotBlank(message: "Le type est requis.")]
    private ?string $type = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(?\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): self
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
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
    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }
    


    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setEvenement($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // Set the owning side to null (unless already changed)
            if ($commentaire->getEvenement() === $this) {
                $commentaire->setEvenement(null);
            }
        }

        return $this;
    }
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'eventsParticipated')]
    #[ORM\JoinTable(name: 'event_participants')]
    private Collection $participants;

    
    // ... autres méthodes ...

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->addEventParticipated($this);
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeEventParticipated($this);
        }

        return $this;
    }
}
