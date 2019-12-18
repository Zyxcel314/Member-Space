<?php

namespace App\Repository;

use App\Entity\Gestionnaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Gestionnaires|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gestionnaires|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gestionnaires[]    findAll()
 * @method Gestionnaires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GestionnairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gestionnaires::class);
    }

    // /**
    //  * @return Gestionnaires[] Returns an array of Gestionnaires objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Gestionnaires
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
