<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Prediction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prediction>
 */
class PredictionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prediction::class);
    }

    public function findByUserAndGame(User $user, Game $game): ?Prediction
    {
        return $this->findOneBy(['predictor' => $user, 'game' => $game]);
    }

    /**
     * @param Game[] $games
     * @return array<int, Prediction> keyed by game id
     */
    public function findByUserForGames(User $user, array $games): array
    {
        $predictions = $this->createQueryBuilder('p')
            ->andWhere('p.predictor = :user')
            ->andWhere('p.game IN (:games)')
            ->setParameter('user', $user)
            ->setParameter('games', $games)
            ->getQuery()
            ->getResult();

        $map = [];
        foreach ($predictions as $prediction) {
            $map[$prediction->getGame()->getId()] = $prediction;
        }

        return $map;
    }

    //    /**
    //     * @return Prediction[] Returns an array of Prediction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Prediction
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
