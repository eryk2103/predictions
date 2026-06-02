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
class PredictionRepository extends ServiceEntityRepository implements PredictionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prediction::class);
    }

    public function findByUserAndGame(User $user, Game $game): ?Prediction
    {
        return $this->findOneBy(['predictor' => $user, 'game' => $game]);
    }

    public function findByUserFiltered(User $user, ?int $competitionId, ?int $phaseId, int $page, int $perPage): array
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.game', 'g')
            ->join('g.competitionPhase', 'ph')
            ->join('ph.Competition', 'c')
            ->andWhere('p.predictor = :user')
            ->setParameter('user', $user)
            ->orderBy('p.id', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        if ($phaseId !== null) {
            $qb->andWhere('ph.id = :phaseId')->setParameter('phaseId', $phaseId);
        } elseif ($competitionId !== null) {
            $qb->andWhere('c.id = :competitionId')->setParameter('competitionId', $competitionId);
        }

        return $qb->getQuery()->getResult();
    }

    public function sumPointsByUser(User $user, ?int $competitionId, ?int $phaseId): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COALESCE(SUM(p.points), 0)')
            ->join('p.game', 'g')
            ->join('g.competitionPhase', 'ph')
            ->join('ph.Competition', 'c')
            ->andWhere('p.predictor = :user')
            ->setParameter('user', $user);

        if ($phaseId !== null) {
            $qb->andWhere('ph.id = :phaseId')->setParameter('phaseId', $phaseId);
        } elseif ($competitionId !== null) {
            $qb->andWhere('c.id = :competitionId')->setParameter('competitionId', $competitionId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countByUserFiltered(User $user, ?int $competitionId, ?int $phaseId): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->join('p.game', 'g')
            ->join('g.competitionPhase', 'ph')
            ->join('ph.Competition', 'c')
            ->andWhere('p.predictor = :user')
            ->setParameter('user', $user);

        if ($phaseId !== null) {
            $qb->andWhere('ph.id = :phaseId')->setParameter('phaseId', $phaseId);
        } elseif ($competitionId !== null) {
            $qb->andWhere('c.id = :competitionId')->setParameter('competitionId', $competitionId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param Game[] $games
     * @return array<int, Prediction> keyed by game id
     */
    public function findByUserForGames(User $user, array $games): array
    {
        $predictions = $this->createQueryBuilder('p')
            ->addSelect('g')
            ->join('p.game', 'g')
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
