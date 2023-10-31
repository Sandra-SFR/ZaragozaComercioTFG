<?php

namespace App\Repository;

use App\Entity\Horario;
use App\Entity\Comercio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Horario>
 *
 * @method Horario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Horario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Horario[]    findAll()
 * @method Horario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Horario[]    findHorarioComercio(array $criteria, array $orderBy = null)
 */
class HorarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Horario::class);
    }

    /**
     * @return Horario[] Returns an array of Horario objects
     */
    public function findHorarioComercio(Comercio $comercio, array $orderBy = null): array
    {
        $qb = $this->createQueryBuilder('h')
            ->select('h.id','c.id as comercioId', 'h.dia', 'h.horaApertura', 'h.horaCierre')
            ->addSelect('CASE h.dia
            WHEN 1 THEN \'Lunes\'
            WHEN 2 THEN \'Martes\'
            WHEN 3 THEN \'Miercoles\'
            WHEN 4 THEN \'Jueves\'
            WHEN 5 THEN \'Viernes\'
            WHEN 6 THEN \'Sabado\'
            WHEN 7 THEN \'Domingo\'
            ELSE \'sin estado\'
            END as nombreDia ')
            ->join('h.comercio', 'c')
            ->where('h.comercio = :val')
            ->setParameter('val', $comercio)
            ->addOrderBy('h.dia', 'ASC')
            ->addOrderBy('h.horaApertura','ASC')
            ;

        return $qb->getQuery()->execute();
    }

//    public function findOneBySomeField($value): ?Horario
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
