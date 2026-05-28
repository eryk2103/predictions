<?php

namespace App\Repository;

use App\Entity\Standing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Standing>
 */
class StandingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Standing::class);
    }

    /** @return Standing[] */
    public function findByCompetition(?int $competitionId = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.position', 'ASC');

        if ($competitionId !== null) {
            $qb->andWhere('s.competition = :competitionId')
               ->setParameter('competitionId', $competitionId);
        }

        return $qb->getQuery()->getResult();
    }
}
