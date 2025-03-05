<?php

namespace App\Entity;

use App\Repository\ModulesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ModulesRepository::class)]
#[Vich\Uploadable]
class Modules
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $titre;

    #[ORM\Column(type: "text")]
    private string $contenu;

    #[ORM\Column(type: "integer")]
    private int $duree;

    #[ORM\ManyToOne(targetEntity: Cours::class, inversedBy: "modules")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;

    #[Vich\UploadableField(mapping: "modules_pdfs", fileNameProperty: "pdfPath")]
    private ?File $pdfFile = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $pdfPath = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;
        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): self
    {
        $this->cours = $cours;
        return $this;
    }

    public function getPdfFile(): ?File
    {
        return $this->pdfFile;
    }

    public function setPdfFile(?File $pdfFile): self
    {
        $this->pdfFile = $pdfFile;

        if ($pdfFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getPdfPath(): ?string
    {
        return $this->pdfPath;
    }

    public function setPdfPath(?string $pdfPath): self
    {
        $this->pdfPath = $pdfPath;
        return $this;
    }
    
}
