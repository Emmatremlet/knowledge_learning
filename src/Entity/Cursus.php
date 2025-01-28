<?php

namespace App\Entity;

use App\Repository\CursusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cursus
 * Représente un cursus éducatif qui peut contenir plusieurs leçons.
 */
#[ORM\Entity(repositoryClass: CursusRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Cursus extends BaseEntity
{
    /**
     * @var int|null ID unique du cursus.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null Nom du cursus.
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var string|null Description du cursus.
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    /**
     * @var float|null Prix du cursus.
     */
    #[ORM\Column]
    private ?float $price = null;

    /**
     * @var Theme|null Thème associé au cursus.
     */
    #[ORM\ManyToOne(inversedBy: 'cursuses')]
    private ?Theme $theme = null;

    /**
     * @var Collection<int, Lesson> Collection des leçons associées au cursus.
     */
    #[ORM\OneToMany(targetEntity: Lesson::class, mappedBy: 'cursus')]
    private Collection $lessons;

    /**
     * Constructeur
     * Initialise la collection de leçons.
     */
    public function __construct()
    {
        $this->lessons = new ArrayCollection();
    }

    /**
     * Retourne l'ID unique du cursus.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom du cursus.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom du cursus.
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
     * Retourne la description du cursus.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description du cursus.
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
     * Retourne le prix du cursus.
     *
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * Définit le prix du cursus.
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
     * Retourne le thème associé au cursus.
     *
     * @return Theme|null
     */
    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    /**
     * Définit le thème associé au cursus.
     *
     * @param Theme|null $theme
     * @return static
     */
    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Retourne les leçons associées au cursus.
     *
     * @return Collection<int, Lesson>
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    /**
     * Ajoute une leçon au cursus.
     *
     * @param Lesson $lesson
     * @return static
     */
    public function addLesson(Lesson $lesson): static
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
            $lesson->setCursus($this);
        }

        return $this;
    }

    /**
     * Supprime une leçon du cursus.
     *
     * @param Lesson $lesson
     * @return static
     */
    public function removeLesson(Lesson $lesson): static
    {
        if ($this->lessons->removeElement($lesson)) {
            // Met à jour la relation côté owning si nécessaire
            if ($lesson->getCursus() === $this) {
                $lesson->setCursus(null);
            }
        }

        return $this;
    }
}