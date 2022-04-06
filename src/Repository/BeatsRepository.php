<?php

namespace App\Repository;

use App\Entity\Beats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Beats|null find($id, $lockMode = null, $lockVersion = null)
 * @method Beats|null findOneBy(array $criteria, array $orderBy = null)
 * @method Beats[]    findAll()
 * @method Beats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BeatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Beats::class);
    }

    public function findSearch($search)
        { 
            return $this->createQueryBuilder('b')
                ->where('b.name LIKE :search')
                ->orWhere('bc.name LIKE :search')
                ->orWhere('bu.pseudo LIKE :search')
                ->orWhere('bu.nom LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->orderBy('b.id', 'ASC')
                ->leftJoin('b.category', 'bc')
                ->addSelect('bc')
                ->leftJoin('b.user', 'bu')
                ->addSelect('bu')
                // ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }
        public function findVIP($VIP){
            return $this->createQueryBuilder('b')
                ->andWhere('b.isVIP = :isVIP')
                ->setParameter('isVIP', $VIP )
                ->getQuery()
                ->getResult()
                ;
        }

    // public function findTest($search, $order)
    //     { 
    //         $qb = $this->createQueryBuilder('b')
    //             ->where('b.name LIKE :search')
    //             ->orWhere('bc.name LIKE :search')
    //             ->orWhere('bu.nom LIKE :search')
    //             ->setParameter('search', '%' . $search . '%')
    //             ->orderBy('b.id', 'ASC')
    //             ->leftJoin('b.category', 'bc')
    //             ->addSelect('bc')
    //             ->leftJoin('b.user', 'bu')
    //             ->addSelect('bu')
    //             // ->setMaxResults(10)
    //         ;
    //         if ($order == true) {
    //             $qb
    //                 ->orderBy('b.id', 'DESC');
    //         }
    //         $qb
    //             ->getQuery()
    //             ->getResult();
    //     }
    // /**
    //  * @return Beats[] Returns an array of Beats objects
    //  */
    /*
    
    */

    /*
    public function findOneBySomeField($value): ?Beats
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
