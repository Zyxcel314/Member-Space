<?php

namespace App\Repository;

use App\Entity\RepresentantFamille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RepresentantFamille|null find($id, $lockMode = null, $lockVersion = null)
 * @method RepresentantFamille|null findOneBy(array $criteria, array $orderBy = null)
 * @method RepresentantFamille[]    findAll()
 * @method RepresentantFamille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepresentantFamilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RepresentantFamille::class);
    }

    // /**
    //  * @return RepresentantFamille[] Returns an array of RepresentantFamille objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RepresentantFamille
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
