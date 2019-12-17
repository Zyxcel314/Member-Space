<?php

namespace App\Repository;

use App\Entity\InformationMajeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method InformationMajeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method InformationMajeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method InformationMajeur[]    findAll()
 * @method InformationMajeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InformationMajeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformationMajeur::class);
    }

    // /**
    //  * @return InformationMajeur[] Returns an array of InformationMajeur objects
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
    public function findOneBySomeField($value): ?InformationMajeur
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
