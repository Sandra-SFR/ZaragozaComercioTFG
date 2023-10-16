<?php

namespace App\Repository;

use App\Entity\Comercio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comercio>
 *
 * @method Comercio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comercio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comercio[]    findAll()
 * @method Comercio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComercioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comercio::class);
    }

//    /**
//     * @return Comercio[] Returns an array of Comercio objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comercio
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
