<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * User
 * Représente un utilisateur de l'application, avec des informations d'authentification et des relations avec d'autres entités.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\HasLifecycleCallbacks]
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface 
{
    /**
     * @var int|null ID unique de l'utilisateur.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null Nom complet de l'utilisateur.
     */
    #[ORM\Column(length: 180)]
    private ?string $name = null;

    /**
     * @var string|null Adresse e-mail unique de l'utilisateur.
     */
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var bool Indique si l'utilisateur a vérifié son compte.
     */
    #[ORM\Column]
    private ?bool $isVerified = false;

    /**
     * @var list<string> Rôles assignés à l'utilisateur.
     */
    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    /**
     * @var string|null Mot de passe haché de l'utilisateur.
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Purchase> Achats associés à l'utilisateur.
     */
    #[ORM\OneToMany(targetEntity: Purchase::class, mappedBy:"user", cascade:["persist", "remove"])]
    private Collection $purchases;

    /**
     * @var Collection<int, Certification> Certifications obtenues par l'utilisateur.
     */
    #[ORM\OneToMany(targetEntity: Certification::class, mappedBy:"user", cascade:["persist", "remove"])]
    private Collection $certifications;

    /**
     * Constructeur
     * Initialise les collections pour les relations.
     */
    public function __construct()
    {
        $this->purchases = new ArrayCollection();
        $this->certifications = new ArrayCollection();
    }

    /**
     * Retourne l'ID unique de l'utilisateur.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom de l'utilisateur.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de l'utilisateur.
     *
     * @param string $name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Retourne l'adresse e-mail de l'utilisateur.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'adresse e-mail de l'utilisateur.
     *
     * @param string $email
     * @return static
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Retourne l'identifiant utilisateur visuel.
     *
     * @see UserInterface
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Retourne les rôles de l'utilisateur.
     *
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur.
     *
     * @param list<string> $roles
     * @return static
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Retourne le mot de passe haché de l'utilisateur.
     *
     * @see PasswordAuthenticatedUserInterface
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe haché de l'utilisateur.
     *
     * @param string $password
     * @return static
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Efface les données sensibles utilisateur.
     *
     * @see UserInterface
     * @return void
     */
    public function eraseCredentials(): void
    {
        // Si des données sensibles temporaires sont stockées, les effacer ici
        // $this->plainPassword = null;
    }

    /**
     * Vérifie si le compte utilisateur est vérifié.
     *
     * @return bool|null
     */
    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    /**
     * Définit si le compte utilisateur est vérifié.
     *
     * @param bool $isVerified
     * @return static
     */
    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Retourne la collection des achats associés à l'utilisateur.
     *
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    /**
     * Retourne la collection des certifications obtenues par l'utilisateur.
     *
     * @return Collection<int, Certification>
     */
    public function getCertifications(): Collection
    {
        return $this->certifications;
    }
}