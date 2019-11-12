<?php

namespace App\Repository;

use App\Entity\ExportDonnees;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ExportDonnees|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExportDonnees|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExportDonnees[]    findAll()
 * @method ExportDonnees[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExportDonneesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExportDonnees::class);
    }

    // /**
    //  * @return ExportDonnees[] Returns an array of ExportDonnees objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExportDonnees
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
