<?php

namespace App\Dto;

use App\Entity\CompetitionPhase;
use App\Entity\Stadium;
use App\Entity\Team;
use App\Enum\GameStatusEnum;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateGameDto
{
    public function __construct(
        #[Assert\NotNull]
        public CompetitionPhase $competitionPhase,

        #[Assert\NotNull]
        public GameStatusEnum $status,

        public ?Team $homeTeam = null,
        public ?Team $awayTeam = null,
        public ?\DateTime $datetime = null,
        public ?Stadium $stadium = null,

        #[Assert\PositiveOrZero]
        public ?int $homeScore = null,

        #[Assert\PositiveOrZero]
        public ?int $awayScore = null,

        #[Assert\PositiveOrZero]
        public ?int $homePenaltyScore = null,

        #[Assert\PositiveOrZero]
        public ?int $awayPenaltyScore = null,
    ) {}
}
