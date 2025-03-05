<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $dateCreation;

    #[ORM\Column(length: 255)]
    private string $statut;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'paniers')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;


    #[ORM\ManyToMany(targetEntity: Cours::class, inversedBy: 'paniers')]
    #[ORM\JoinTable(name: 'panier_cours')]
    private Collection $cours;


    #[ORM\OneToOne(mappedBy: 'panier', targetEntity: Commande::class)]
    private ?Commande $commande = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();  
        $this->statut = 'in progress..';  
        $this->cours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): \DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Cours>
     */
     public function getCours(): Collection
     {
        return $this->cours;
    }

    

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        // Set (or unset) the owning side of the relation if necessary
        if ($commande !== null && $commande->getPanier() !== $this) {
            $commande->setPanier($this);
        }

        return $this;
    }













    
 public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours[] = $cour;
            $cour->addPanier($this); // Appeler addPanier sur l'entité Cours
        }
        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            $cour->removePanier($this); // Appeler removePanier sur l'entité Cours
        }
        return $this;
    }
}
