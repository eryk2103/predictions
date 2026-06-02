<?php

namespace App\Dto;

use App\Enum\PhaseTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateCompetitionPhaseDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $name,

        #[Assert\NotNull]
        public PhaseTypeEnum $type,

        #[Assert\NotNull]
        #[Assert\Positive]
        public int $sequence,

        public bool $isCurrent = false,
    ) {}
}
