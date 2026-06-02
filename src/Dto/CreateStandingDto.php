<?php

namespace App\Dto;

use App\Entity\Competition;
use App\Entity\Team;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateStandingDto
{
    public function __construct(
        #[Assert\NotNull]
        public Competition $competition,

        #[Assert\NotNull]
        public Team $team,

        #[Assert\NotNull]
        #[Assert\Positive]
        public int $position,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public int $played,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public int $won,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public int $drawn,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public int $lost,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public int $goals,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public int $goalsAgainst,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public int $points,
    ) {}
}
