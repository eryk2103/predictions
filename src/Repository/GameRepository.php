<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /** @return Game[] */
    public function findByCurrentPhases(): array
    {
        return $this->createQueryBuilder('g')
            ->join('g.competitionPhase', 'p')
            ->andWhere('p.isCurrent = true')
            ->orderBy('g.datetime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Game[] */
    public function findAllByCompetition(int $competitionId): array
    {
        return $this->createQueryBuilder('g')
            ->join('g.competitionPhase', 'p')
            ->join('p.Competition', 'c')
            ->andWhere('c.id = :competitionId')
            ->setParameter('competitionId', $competitionId)
            ->orderBy('p.sequence', 'ASC')
            ->addOrderBy('g.datetime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByFilters(?int $competitionId = null, ?int $phaseId = null, int $page = 1, int $perPage = 20): Paginator
    {
        $qb = $this->createQueryBuilder('g')
            ->join('g.competitionPhase', 'p')
            ->join('p.Competition', 'c')
            ->orderBy('g.datetime', 'ASC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        if ($phaseId !== null) {
            $qb->andWhere('p.id = :phaseId')
               ->setParameter('phaseId', $phaseId);
        } elseif ($competitionId !== null) {
            $qb->andWhere('c.id = :competitionId')
               ->setParameter('competitionId', $competitionId);
        }

        return new Paginator($qb);
    }
}
