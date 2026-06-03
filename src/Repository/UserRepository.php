<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array<int, array{id: int, username: string, points: int}>
     */
    public function getRankings(?int $competitionId = null, ?int $phaseId = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.id, u.username, COALESCE(SUM(p.points), 0) AS points')
            ->leftJoin('u.predictions', 'p');

        if ($phaseId !== null) {
            $qb->leftJoin('p.game', 'g')
               ->leftJoin('g.competitionPhase', 'ph')
               ->andWhere('ph.id = :phaseId')
               ->setParameter('phaseId', $phaseId);
        } elseif ($competitionId !== null) {
            $qb->leftJoin('p.game', 'g')
               ->leftJoin('g.competitionPhase', 'ph')
               ->leftJoin('ph.Competition', 'c')
               ->andWhere('c.id = :competitionId')
               ->setParameter('competitionId', $competitionId);
        }

        return $qb
            ->groupBy('u.id')
            ->orderBy('points', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
