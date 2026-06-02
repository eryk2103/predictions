<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdatePredictionDto
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public int $homeScore,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public int $awayScore,

        #[Assert\PositiveOrZero]
        public ?int $homePenalty = null,

        #[Assert\PositiveOrZero]
        public ?int $awayPenalty = null,
    ) {}
}
