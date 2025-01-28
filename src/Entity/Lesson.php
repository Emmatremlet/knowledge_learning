<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Lesson
 * Représente une leçon individuelle faisant partie d'un cursus.
 */
#[ORM\Entity(repositoryClass: LessonRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Lesson extends BaseEntity
{
    /**
     * @var int|null ID unique de la leçon.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null Nom de la leçon.
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var string|null Description de la leçon.
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    /**
     * @var string|null URL de la vidéo associée à la leçon.
     */
    #[ORM\Column(length: 255)]
    private ?string $videoUrl = null;

    /**
     * @var float|null Prix de la leçon.
     */
    #[ORM\Column]
    private ?float $price = null;

    /**
     * @var Cursus|null Cursus auquel la leçon est associée.
     */
    #[ORM\ManyToOne(inversedBy: 'lessons')]
    private ?Cursus $cursus = null;

    /**
     * @var bool Indique si la leçon a été validée.
     */
    #[ORM\Column(options: ['default' => false])]
    private bool $isValidated = false;

    /**
     * Vérifie si la leçon a été validée.
     *
     * @return bool
     */
    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    /**
     * Définit si la leçon est validée.
     *
     * @param bool $isValidated
     * @return self
     */
    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;
        return $this;
    }

    /**
     * Retourne l'ID unique de la leçon.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom de la leçon.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de la leçon.
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
     * Retourne la description de la leçon.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description de la leçon.
     *
     * @param string|null $description
     * @return static
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Retourne l'URL de la vidéo associée à la leçon.
     *
     * @return string|null
     */
    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    /**
     * Définit l'URL de la vidéo associée à la leçon.
     *
     * @param string $videoUrl
     * @return static
     */
    public function setVideoUrl(string $videoUrl): static
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }

    /**
     * Retourne le prix de la leçon.
     *
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * Définit le prix de la leçon.
     *
     * @param float $price
     * @return static
     */
    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Retourne le cursus auquel la leçon est associée.
     *
     * @return Cursus|null
     */
    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    /**
     * Définit le cursus auquel la leçon est associée.
     *
     * @param Cursus|null $cursus
     * @return static
     */
    public function setCursus(?Cursus $cursus): static
    {
        $this->cursus = $cursus;

        return $this;
    }
}