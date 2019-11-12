<?php

namespace App\Repository;

use App\Entity\FAMILLE;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FAMILLE|null find($id, $lockMode = null, $lockVersion = null)
 * @method FAMILLE|null findOneBy(array $criteria, array $orderBy = null)
 * @method FAMILLE[]    findAll()
 * @method FAMILLE[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FAMILLERepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FAMILLE::class);
    }

    // /**
    //  * @return FAMILLE[] Returns an array of FAMILLE objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FAMILLE
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
