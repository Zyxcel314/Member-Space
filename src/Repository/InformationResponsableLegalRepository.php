<?php

namespace App\Repository;

use App\Entity\InformationResponsableLegal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method InformationResponsableLegal|null find($id, $lockMode = null, $lockVersion = null)
 * @method InformationResponsableLegal|null findOneBy(array $criteria, array $orderBy = null)
 * @method InformationResponsableLegal[]    findAll()
 * @method InformationResponsableLegal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InformationResponsableLegalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformationResponsableLegal::class);
    }

    // /**
    //  * @return InformationResponsableLegal[] Returns an array of InformationResponsableLegal objects
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
    public function findOneBySomeField($value): ?InformationResponsableLegal
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
