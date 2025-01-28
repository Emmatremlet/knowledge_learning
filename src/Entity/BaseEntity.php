<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseEntity
 * Classe abstraite fournissant des propriétés et des méthodes communes pour la gestion des métadonnées des entités.
 */
abstract class BaseEntity
{
    /**
     * @var \DateTimeImmutable|null Date et heure de création de l'entité.
     */
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var \DateTimeImmutable|null Date et heure de la dernière mise à jour de l'entité.
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var string|null Nom de l'utilisateur ayant créé l'entité.
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $createdBy = null;

    /**
     * @var string|null Nom de l'utilisateur ayant modifié l'entité pour la dernière fois.
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $updatedBy = null;

    /**
     * Retourne la date et l'heure de création de l'entité.
     *
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Définit la date et l'heure de création de l'entité.
     *
     * @param \DateTimeImmutable $createdAt
     * @return self
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Retourne la date et l'heure de la dernière mise à jour de l'entité.
     *
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Définit la date et l'heure de la dernière mise à jour de l'entité.
     *
     * @param \DateTimeImmutable|null $updatedAt
     * @return self
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Retourne le nom de l'utilisateur ayant créé l'entité.
     *
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    /**
     * Définit le nom de l'utilisateur ayant créé l'entité.
     *
     * @param string|null $createdBy
     * @return self
     */
    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * Retourne le nom de l'utilisateur ayant modifié l'entité pour la dernière fois.
     *
     * @return string|null
     */
    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    /**
     * Définit le nom de l'utilisateur ayant modifié l'entité pour la dernière fois.
     *
     * @param string|null $updatedBy
     * @return self
     */
    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    /**
     * Définit automatiquement les dates de création et de mise à jour avant l'insertion dans la base de données.
     *
     * @return void
     */
    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $now = new \DateTimeImmutable();
        if ($this->createdAt === null) {
            $this->createdAt = $now;
        }
        $this->updatedAt = $now;
    }

    /**
     * Met à jour automatiquement la date de modification avant une mise à jour dans la base de données.
     *
     * @return void
     */
    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Définit automatiquement l'utilisateur qui a créé ou modifié l'entité.
     *
     * @param string|null $username Nom de l'utilisateur.
     * @return void
     */
    public function setDefaultUser(?string $username): void
    {
        if ($this->createdBy === null) {
            $this->createdBy = $username;
        }
        $this->updatedBy = $username;
    }
}