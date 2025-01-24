<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Theme>
 */
class ThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }

    /**
     * Retourne les deux thèmes avec le prix total des cursus le plus élevé
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
    
//    /**
//     * @return Theme[] Returns an array of Theme objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Theme
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}