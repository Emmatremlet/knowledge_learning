<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Purchase
 * Représente un achat effectué par un utilisateur, lié à une leçon ou un cursus.
 */
#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Purchase extends BaseEntity
{
    /**
     * @var int|null ID unique de l'achat.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var User|null Utilisateur ayant effectué l'achat.
     */
    #[ORM\ManyToOne(inversedBy: 'purchases')]
    private ?User $user = null;

    /**
     * @var Lesson|null Leçon associée à l'achat.
     */
    #[ORM\ManyToOne(targetEntity: Lesson::class)]
    private ?Lesson $lesson = null;

    /**
     * @var Cursus|null Cursus associé à l'achat.
     */
    #[ORM\ManyToOne(targetEntity: Cursus::class)]
    private ?Cursus $cursus = null;

    /**
     * @var \DateTimeImmutable|null Date de l'achat.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $purchaseDate = null;

    /**
     * @var string|null Statut de l'achat (par exemple, "pending", "completed").
     */
    #[ORM\Column(length: 20)]
    private ?string $status = 'pending';

    /**
     * @var float|null Prix total de l'achat.
     */
    #[ORM\Column]
    private ?float $totalPrice = null;

    /**
     * Retourne l'ID unique de l'achat.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'utilisateur associé à l'achat.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Définit l'utilisateur associé à l'achat.
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
     * Retourne la leçon associée à l'achat.
     *
     * @return Lesson|null
     */
    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    /**
     * Définit la leçon associée à l'achat.
     *
     * @param Lesson|null $lesson
     * @return self
     */
    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;
        return $this;
    }

    /**
     * Retourne le cursus associé à l'achat.
     *
     * @return Cursus|null
     */
    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    /**
     * Définit le cursus associé à l'achat.
     *
     * @param Cursus|null $cursus
     * @return self
     */
    public function setCursus(?Cursus $cursus): self
    {
        $this->cursus = $cursus;
        return $this;
    }

    /**
     * Retourne la date de l'achat.
     *
     * @return \DateTimeInterface|null
     */
    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchaseDate;
    }

    /**
     * Définit la date de l'achat.
     *
     * @param \DateTimeInterface $purchaseDate
     * @return static
     */
    public function setPurchaseDate(\DateTimeInterface $purchaseDate): static
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * Retourne le prix total de l'achat.
     *
     * @return float|null
     */
    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    /**
     * Définit le prix total de l'achat.
     *
     * @param float $totalPrice
     * @return static
     */
    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Retourne le statut de l'achat.
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Définit le statut de l'achat.
     *
     * @param string $status
     * @return static
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
