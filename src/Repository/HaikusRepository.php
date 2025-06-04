<?php

namespace App\Repository;

use App\Entity\Haikus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Haikus>
 */
class HaikusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Haikus::class);
    }

    //    /**
    //     * @return Haikus[] Returns an array of Haikus objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Haikus
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // Affichage de tous les haikus sauf ceux de la personne connectée pour le feed. 
    public function findAllExceptByUser($user) 
    {
        return $this->createQueryBuilder('h')
            ->where('h.creator != :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function numberOfUserHaiku($user)
    {
        return $this->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->where('h.creator = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllHaikusLikedByUser($user)
    {
        return $this->createQueryBuilder('h')
            ->join('h.likes', 'l') // 'likes' = nom de la propriété dans Haikus (ManyToOne inverse)
            ->where('l.sender = :user')
            ->andWhere('h.creator != :user') // facultatif si tu veux exclure ses propres haikus
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
