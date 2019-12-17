<?php

namespace App\Repository;

use App\Entity\InformationsFamille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method InformationsFamille|null find($id, $lockMode = null, $lockVersion = null)
 * @method InformationsFamille|null findOneBy(array $criteria, array $orderBy = null)
 * @method InformationsFamille[]    findAll()
 * @method InformationsFamille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InformationsFamilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformationsFamille::class);
    }

    // /**
    //  * @return InformationsFamille[] Returns an array of InformationsFamille objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InformationsFamille
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
