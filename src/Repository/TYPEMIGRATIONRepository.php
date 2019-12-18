<?php

namespace App\Repository;

use App\Entity\TYPECOTISATION;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TYPECOTISATION|null find($id, $lockMode = null, $lockVersion = null)
 * @method TYPECOTISATION|null findOneBy(array $criteria, array $orderBy = null)
 * @method TYPECOTISATION[]    findAll()
 * @method TYPECOTISATION[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TYPEMIGRATIONRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TYPECOTISATION::class);
    }

    // /**
    //  * @return TYPECOTISATION[] Returns an array of TYPECOTISATION objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TYPECOTISATION
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
