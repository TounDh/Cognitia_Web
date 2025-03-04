<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez entrer un email.', groups: ['RegistrationUser', 'RegistrationApprenant', 'RegistrationInstructeur'])]
    #[Assert\Email(message: 'L\'email doit être valide.', groups: ['RegistrationUser', 'RegistrationApprenant', 'RegistrationInstructeur'])]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $lastConnexion = null;

    // Champs communs
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre prénom.', groups: ['RegistrationUser', 'RegistrationApprenant', 'RegistrationInstructeur'])]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre nom.', groups: ['RegistrationUser', 'RegistrationApprenant', 'RegistrationInstructeur'])]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre numéro de téléphone.', groups: ['RegistrationUser', 'RegistrationApprenant', 'RegistrationInstructeur'])]
    private ?string $phoneNumber = null;

    // Champs spécifiques à Apprenant
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre niveau.', groups: ['RegistrationApprenant'])]
    private ?string $level = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $interests = [];

    

    #[ORM\OneToMany(mappedBy: 'apprenant', targetEntity: Evaluation::class)]
    private Collection $evaluations;

    // Champs spécifiques à Instructeur
    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre biographie.', groups: ['RegistrationInstructeur'])]
    private ?string $biographie = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre photo.', groups: ['RegistrationInstructeur'])]
    private ?string $photo = null;

    

    #[ORM\OneToMany(mappedBy: 'instructeur', targetEntity: Quiz::class)]
    private Collection $quizzes;

    #[ORM\OneToMany(mappedBy: 'apprenant', targetEntity: Certificat::class, cascade: ['remove'])]
    private Collection $certificats;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->eventsParticipated = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
        $this->roles = ['ROLE_USER']; // Rôle par défaut
        $this->certificats = new ArrayCollection();
    }

    /**
     * @return Collection<int, Certificat>
     */
    public function getCertificats(): Collection
    {
        return $this->certificats;
    }

    public function addCertificat(Certificat $certificat): self
    {
        if (!$this->certificats->contains($certificat)) {
            $this->certificats[] = $certificat;
            $certificat->setApprenant($this);
        }
        return $this;
    }

    public function removeCertificat(Certificat $certificat): self
    {
        if ($this->certificats->removeElement($certificat)) {
            if ($certificat->getApprenant() === $this) {
                $certificat->setApprenant(null);
            }
        }
        return $this;
    }

    

    // Getters et Setters pour les champs communs
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastConnexion(): ?\DateTime
    {
        return $this->lastConnexion;
    }

    public function setLastConnexion(?\DateTime $lastConnexion): self
    {
        $this->lastConnexion = $lastConnexion;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getQuizzes(): Collection { return $this->quizzes; }


    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    // Getters et Setters pour les champs spécifiques à Apprenant
    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): self
    {
        $this->level = $level;
        return $this;
    }

    public function getInterests(): ?array
    {
        return $this->interests;
    }

    public function setInterests(?array $interests): self
    {
        $this->interests = $interests;
        return $this;
    }

    

    

    // Getters et Setters pour les champs spécifiques à Instructeur
    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(?string $biographie): self
    {
        $this->biographie = $biographie;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }
    

    

    // Méthodes de l'interface UserInterface
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void
    {
        // Effacer les données sensibles temporaires
    }
    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'participants')]
    private Collection $eventsParticipated;

   
    // ... autres méthodes ...

    /**
     * @return Collection<int, Event>
     */
    public function getEventsParticipated(): Collection
    {
        return $this->eventsParticipated;
    }

    public function addEventParticipated(Event $event): self
    {
        if (!$this->eventsParticipated->contains($event)) {
            $this->eventsParticipated[] = $event;
            $event->addParticipant($this);
        }

        return $this;
    }

    public function removeEventParticipated(Event $event): self
    {
        if ($this->eventsParticipated->removeElement($event)) {
            $event->removeParticipant($this);
        }

        return $this;
    }
}