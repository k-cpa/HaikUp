<?php

namespace App\Repository;

use App\Entity\UserWords;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserWords>
 */
class UserWordsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWords::class);
    }

    //    /**
    //     * @return UserWords[] Returns an array of UserWords objects
    //     */
       public function findPendingPacksForUser($user): array
       {
           return $this->createQueryBuilder('u')
                ->select('IDENTITY (u.sender) AS sender_id, u.created_at')
                ->join('u.Words', 'w')
               ->andWhere('u.Receiver IS NULL')
               ->andWhere('u.status = :status')
               ->andWhere('u.sender != :user')
               ->setParameter('status', 'pending')
               ->setParameter('user', $user)
               ->groupBy('u.sender', 'u.created_at')
               ->having('COUNT(u.id) = 3')
               ->getQuery()
               ->getResult()
           ;
       }

    //    public function findOneBySomeField($value): ?UserWords
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
