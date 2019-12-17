<?php

namespace App\Repository;

use App\Entity\ADHERENT;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ADHERENT|null find($id, $lockMode = null, $lockVersion = null)
 * @method ADHERENT|null findOneBy(array $criteria, array $orderBy = null)
 * @method ADHERENT[]    findAll()
 * @method ADHERENT[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ADHERENTRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ADHERENT::class);
    }

    // /**
    //  * @return ADHERENT[] Returns an array of ADHERENT objects
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
    public function findOneBySomeField($value): ?ADHERENT
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
