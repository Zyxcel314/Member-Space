<?php

namespace App\Repository;

use App\Entity\InformationsMineur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method InformationsMineur|null find($id, $lockMode = null, $lockVersion = null)
 * @method InformationsMineur|null findOneBy(array $criteria, array $orderBy = null)
 * @method InformationsMineur[]    findAll()
 * @method InformationsMineur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InformationsMineurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformationsMineur::class);
    }

    // /**
    //  * @return InformationsMineur[] Returns an array of InformationsMineur objects
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
    public function findOneBySomeField($value): ?InformationsMineur
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
