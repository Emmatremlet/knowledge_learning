<?php

namespace App\Repository;

use App\Entity\Certification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class CertificationRepository
 *
 * Ce repository gère les requêtes pour l'entité Certification.
 * Il étend la classe ServiceEntityRepository fournie par Doctrine.
 *
 * @extends ServiceEntityRepository<Certification>
 * @package App\Repository
 */
class CertificationRepository extends ServiceEntityRepository
{
    /**
     * CertificationRepository constructor.
     *
     * Initialise le repository avec le gestionnaire de registre pour l'entité Certification.
     *
     * @param ManagerRegistry $registry Le gestionnaire de registre Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Certification::class);
    }
}