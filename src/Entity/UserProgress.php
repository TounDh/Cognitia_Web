<?php
namespace App\Entity;

use App\Repository\UserProgressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProgressRepository::class)]
class UserProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Modules::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Modules $module = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isOpened = false;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
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

    public function getModule(): ?Modules
    {
        return $this->module;
    }

    public function setModule(?Modules $module): self
    {
        $this->module = $module;
        return $this;
    }

    public function isOpened(): bool
    {
        return $this->isOpened;
    }

    public function setIsOpened(bool $isOpened): self
    {
        $this->isOpened = $isOpened;
        return $this;
    }
}