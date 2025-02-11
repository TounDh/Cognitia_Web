<?php

namespace App\Entity;

use App\Repository\DefisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DefisRepository::class)]
class Defis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
