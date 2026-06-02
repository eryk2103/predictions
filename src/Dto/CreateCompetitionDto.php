<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateCompetitionDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $name,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $shortName,

        #[Assert\NotNull]
        public int $startYear,

        #[Assert\NotNull]
        public int $endYear,

        #[Assert\Length(max: 255)]
        public ?string $logo = null,
    ) {}
}
