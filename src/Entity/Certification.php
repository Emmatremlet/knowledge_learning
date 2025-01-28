<?php

namespace App\Entity;

use App\Repository\CertificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Certification
 * Représente une certification attribuée à un utilisateur pour un cursus spécifique.
 */
#[ORM\Entity(repositoryClass: CertificationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Certification extends BaseEntity
{
    /**
     * @var int|null ID unique de la certification.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var User|null Utilisateur auquel la certification est attribuée.
     */
    #[ORM\ManyToOne(inversedBy: 'certifications')]
    private ?User $user = null;

    /**
     * @var Cursus|null Cursus associé à la certification.
     */
    #[ORM\ManyToOne]
    private ?Cursus $cursus = null;

    /**
     * @var bool|null Indique si la certification a été validée.
     */
    #[ORM\Column]
    private ?bool $isValidated = null;

    /**
     * @var \DateTimeImmutable|null Date de validation de la certification.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $validatedAt = null;

    /**
     * Retourne l'ID unique de la certification.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'utilisateur associé à la certification.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Définit l'utilisateur associé à la certification.
     *
     * @param User|null $user
     * @return static
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Retourne le cursus associé à la certification.
     *
     * @return Cursus|null
     */
    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    /**
     * Définit le cursus associé à la certification.
     *
     * @param Cursus|null $cursus
     * @return static
     */
    public function setCursus(?Cursus $cursus): static
    {
        $this->cursus = $cursus;

        return $this;
    }

    /**
     * Vérifie si la certification a été validée.
     *
     * @return bool|null
     */
    public function isValidated(): ?bool
    {
        return $this->isValidated;
    }

    /**
     * Définit si la certification est validée.
     *
     * @param bool $isValidated
     * @return static
     */
    public function setIsValidated(bool $isValidated): static
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    /**
     * Retourne la date de validation de la certification.
     *
     * @return \DateTimeImmutable|null
     */
    public function getValidatedAt(): ?\DateTimeImmutable
    {
        return $this->validatedAt;
    }

    /**
     * Définit la date de validation de la certification.
     *
     * @param \DateTimeImmutable $validatedAt
     * @return static
     */
    public function setValidatedAt(\DateTimeImmutable $validatedAt): static
    {
        $this->validatedAt = $validatedAt;

        return $this;
    }
}