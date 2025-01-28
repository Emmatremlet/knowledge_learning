<?php

namespace App\Repository;

use App\Entity\Cursus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class CursusRepository
 *
 * Ce repository gère les requêtes pour l'entité Cursus.
 * Il étend la classe ServiceEntityRepository fournie par Doctrine.
 *
 * @extends ServiceEntityRepository<Cursus>
 * @package App\Repository
 */
class CursusRepository extends ServiceEntityRepository
{
    /**
     * CursusRepository constructor.
     *
     * Initialise le repository avec le gestionnaire de registre pour l'entité Cursus.
     *
     * @param ManagerRegistry $registry Le gestionnaire de registre Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cursus::class);
    }
}