<?php

namespace App\Repository;

use App\Entity\InformationEmployeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method InformationEmployeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method InformationEmployeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method InformationEmployeur[]    findAll()
 * @method InformationEmployeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InformationEmployeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformationEmployeur::class);
    }

    // /**
    //  * @return InformationEmployeur[] Returns an array of InformationEmployeur objects
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
    public function findOneBySomeField($value): ?InformationEmployeur
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
