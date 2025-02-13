<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    private float $montant;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $datePaiement;

    #[ORM\Column(length: 255)]
    private string $methode;

    #[ORM\OneToOne(targetEntity: Commande::class, inversedBy: 'paiement')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    public function __construct()
    {
        $this->datePaiement = new \DateTime();  // Par défaut, la date de paiement est la date actuelle
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDatePaiement(): \DateTime
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTime $datePaiement): self
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getMethode(): string
    {
        return $this->methode;
    }

    public function setMethode(string $methode): self
    {
        $this->methode = $methode;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }
}

