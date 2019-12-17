<?php

namespace App\Repository;

use App\Entity\AutorisationSortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AutorisationSortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method AutorisationSortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method AutorisationSortie[]    findAll()
 * @method AutorisationSortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutorisationSortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutorisationSortie::class);
    }

    // /**
    //  * @return AutorisationSortie[] Returns an array of AutorisationSortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AutorisationSortie
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
