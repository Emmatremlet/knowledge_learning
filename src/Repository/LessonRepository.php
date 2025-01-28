<?php

namespace App\Repository;

use App\Entity\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class LessonRepository
 *
 * Ce repository gère les requêtes pour l'entité Lesson.
 * Il étend la classe ServiceEntityRepository fournie par Doctrine.
 *
 * @extends ServiceEntityRepository<Lesson>
 * @package App\Repository
 */
class LessonRepository extends ServiceEntityRepository
{
    /**
     * LessonRepository constructor.
     *
     * Initialise le repository avec le gestionnaire de registre pour l'entité Lesson.
     *
     * @param ManagerRegistry $registry Le gestionnaire de registre Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }
}