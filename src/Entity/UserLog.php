<?php
// src/Entity/UserLog.php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user_log')]
class UserLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)] // Cette ligne assure que chaque log est associé à un User
    private User $user;

    #[ORM\Column(type: 'string', length: 50)]
    private string $action;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $details = null;

    #[ORM\Column(type:"json", nullable:true)]
    private $modifiedFields;

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }


    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
    

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;
        return $this;
    }


    public function getDecodedDetails(): array
    {
        return json_decode($this->details, true) ?? [];
    }


    public function getModifiedFields(): ?array
    {
        return $this->modifiedFields;
    }

    public function setModifiedFields(?array $modifiedFields): self
    {
        $this->modifiedFields = $modifiedFields;

        return $this;
    }
}