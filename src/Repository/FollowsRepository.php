<?php

namespace App\Repository;

use App\Entity\Follows;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Follows>
 */
class FollowsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Follows::class);
    }

    //    /**
    //     * @return Follows[] Returns an array of Follows objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Follows
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

        public function countFollowers(User $user): int
        {
            return $this->createQueryBuilder('f')
                ->select('COUNT(f.id)')
                ->where('f.Receiver = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getSingleScalarResult(); // Utile pour récupérer une valeur scale (ici un nombre)
        }

        public function countFollows(User $user): int
        {
            return $this->createQueryBuilder('f')
                ->select('COUNT(f.id)')
                ->where('f.Sender = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getSingleScalarResult(); // Utile pour récupérer une valeur scale (ici un nombre)
        }
}
