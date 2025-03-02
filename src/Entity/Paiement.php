<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


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


    #[ORM\Column(length: 255)]
    private string $cardNumber;

    #[ORM\Column(length: 255)]  // Ajouté pour le titulaire de la carte
    private string $cardHolder;

    #[ORM\Column(length: 5)]  // Ajouté pour la date d'expiration (MM/YY)
    private string $expiryDate;

    #[ORM\Column(length: 4)]  // Ajouté pour le CVV
    private string $cvv;


    #[ORM\OneToOne(targetEntity: Commande::class, inversedBy: 'paiement')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
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


    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    public function setCardNumber(string $cardNumber): self
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    public function getCardHolder(): string
    {
        return $this->cardHolder;
    }

    public function setCardHolder(string $cardHolder): self
    {
        $this->cardHolder = $cardHolder;
        return $this;
    }

    public function getExpiryDate(): string
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(string $expiryDate): self
    {
        $this->expiryDate = $expiryDate;
        return $this;
    }

    public function getCvv(): string
    {
        return $this->cvv;
    }

    public function setCvv(string $cvv): self
    {
        $this->cvv = $cvv;
        return $this;
    }
}

