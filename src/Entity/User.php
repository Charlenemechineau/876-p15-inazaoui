<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
// Permet à Symfony de gérer l’authentification par mot de passe.
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
// Permet à Symfony de reconnaître cette entité comme un utilisateur.
use Symfony\Component\Security\Core\User\UserInterface;
// Permet à Symfony de vérifier que l’adresse e-mail est unique en base de données.
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
// Permet à Symfony de valider les données de l’entité User.
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse e-mail est déjà utilisée.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private bool $admin = false;

    // Permet de savoir si un utilisateur est bloqué.
    // Un utilisateur bloqué ne peut plus se connecter à l'application.
    #[ORM\Column]
    private bool $blocked = false;

    #[ORM\Column]
    private ?string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: 'L’adresse e-mail n’est pas valide.')]
    private ?string $email = null;

    //me permet de  stocker les rôles de l'utilisateur dans la base de données sous forme de tableau JSON
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    // me permet de stocker le mot de passe haché de l'utilisateur dans la base de données
    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'user')]
    private Collection $medias;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function setMedias(Collection $medias): void
    {
        $this->medias = $medias;
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): void
    {
        $this->admin = $admin;
    }

    public function isBlocked(): bool
    {
        return $this->blocked;
    }

    public function setBlocked(bool $blocked): static
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = $this->admin ? 'ROLE_ADMIN' : 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        if ($this->email === null || $this->email === '') {
            throw new \LogicException(
                'L’adresse e-mail de l’utilisateur ne peut pas être vide.'
            );
        }

        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }
}
