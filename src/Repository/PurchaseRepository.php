<?php

namespace App\Repository;

use App\Entity\Purchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PurchaseRepository
 *
 * Ce repository gère les requêtes pour l'entité Purchase.
 * Il étend la classe ServiceEntityRepository fournie par Doctrine et inclut des méthodes personnalisées pour des recherches spécifiques.
 *
 * @extends ServiceEntityRepository<Purchase>
 * @package App\Repository
 */
class PurchaseRepository extends ServiceEntityRepository
{
    /**
     * PurchaseRepository constructor.
     *
     * Initialise le repository avec le gestionnaire de registre pour l'entité Purchase.
     *
     * @param ManagerRegistry $registry Le gestionnaire de registre Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    /**
     * Trouve les achats effectués par un utilisateur spécifique.
     *
     * Cette méthode retourne tous les objets Purchase associés à un utilisateur donné.
     *
     * @param mixed $user L'utilisateur pour lequel récupérer les achats.
     * @return Purchase[] Retourne un tableau d'objets Purchase correspondant aux critères.
     */
    public function findByUser($user): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}