<?php

namespace App\Entity;

use App\Repository\PanierRepository;
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

    #[ORM\ManyToOne(targetEntity: Apprenant::class, inversedBy: 'paniers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Apprenant $apprenant = null;

    #[ORM\OneToMany(mappedBy: 'panier', targetEntity: Cours::class)]
    private iterable $cours;

    #[ORM\OneToOne(mappedBy: 'panier', targetEntity: Commande::class)]
    private ?Commande $commande = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();  // Par défaut, la date de création est la date actuelle
        $this->statut = 'en attente';  // Par défaut, le statut peut être 'en attente'
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

    public function getApprenant(): ?Apprenant
    {
        return $this->apprenant;
    }

    public function setApprenant(?Apprenant $apprenant): self
    {
        $this->apprenant = $apprenant;

        return $this;
    }

    public function getCours(): iterable
    {
        return $this->cours;
    }

    public function setCours(iterable $cours): self
    {
        $this->cours = $cours;

        return $this;
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
}
