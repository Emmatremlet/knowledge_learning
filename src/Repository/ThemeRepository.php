<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ThemeRepository
 *
 * Ce repository gère les requêtes pour l'entité Theme.
 * Il étend la classe ServiceEntityRepository fournie par Doctrine et inclut des méthodes personnalisées pour des recherches spécifiques.
 *
 * @extends ServiceEntityRepository<Theme>
 * @package App\Repository
 */
class ThemeRepository extends ServiceEntityRepository
{
    /**
     * ThemeRepository constructor.
     *
     * Initialise le repository avec le gestionnaire de registre pour l'entité Theme.
     *
     * @param ManagerRegistry $registry Le gestionnaire de registre Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }

    /**
     * Trouve les deux thèmes ayant le prix total des cursus le plus élevé.
     *
     * Cette méthode calcule la somme des prix des cursus associés à chaque thème,
     * trie les résultats par ordre décroissant et retourne les deux thèmes en tête.
     *
     * @return array Retourne un tableau contenant les deux thèmes avec leur prix total de cursus.
     */
    public function findTopTwoThemesByTotalCursusPrice(): array
    {
        return $this->createQueryBuilder('t')
            ->select('t, SUM(c.price) as total_price')
            ->leftJoin('t.cursuses', 'c')
            ->groupBy('t.id')
            ->orderBy('total_price', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère tous les thèmes avec leurs cursus et leurs leçons.
     *
     * Cette méthode charge tous les thèmes, en incluant leurs cursus associés
     * ainsi que les leçons liées à ces cursus via des jointures.
     *
     * @return array Retourne un tableau contenant tous les thèmes avec leurs relations.
     */
    public function getAll(): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.cursuses', 'c') 
            ->addSelect('c') 
            ->leftJoin('c.lessons', 'l') 
            ->addSelect('l') 
            ->getQuery()
            ->getResult();
    }
}