<?php

namespace App\Repository;

use App\Entity\Comercio;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comercio>
 *
 * @method Comercio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comercio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comercio[]    findAll()
 * @method Comercio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Comercio[]    findNombresComercios(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComercioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comercio::class);
    }



    /**
     * @return Comercio[] Returns an array of Comercio objects
     */
    public function findNombresComercios(Usuario $usuario, array $orderBy = null, $limit = null, $offset = null ): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id, c.nombre', 'u.nombre as usuario', 'c.email', 'c.estado', 'f.archivo' )
            ->addSelect('CASE c.estado
            WHEN 1 THEN \'abierto\'
            WHEN 2 THEN \'cerrado\'
            WHEN 3 THEN \'vacaciones\'
            ELSE \'sin estado\'
            END as nombreEstado ')
            ->join('c.usuario', 'u')
//            ->leftJoin('c.fotos', 'f')
            ->leftJoin('c.fotos', 'f', 'WITH', 'f.destacada = true')
            ->where('c.usuario = :val')
            ->setParameter('val', $usuario);

        if(!is_null($orderBy)){
            foreach ($orderBy as $campo => $direccion){
                $qb->orderBy('c.' .$campo, $direccion);
            }
        }

        if(!is_null($limit)){
            $qb->setMaxResults($limit);
        }

        if(!is_null($offset)){
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->execute();
    }

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
