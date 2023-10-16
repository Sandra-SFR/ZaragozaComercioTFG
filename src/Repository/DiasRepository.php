<?php

namespace App\Repository;

use App\Entity\Dias;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dias>
 *
 * @method Dias|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dias|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dias[]    findAll()
 * @method Dias[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dias::class);
    }

//    /**
//     * @return Dias[] Returns an array of Dias objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Dias
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
