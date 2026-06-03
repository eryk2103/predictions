<?php

namespace App\Dto;

use App\Entity\Country;
use App\Entity\Stadium;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateTeamDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $name,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $shortName,

        #[Assert\NotBlank]
        #[Assert\Length(max: 3)]
        public string $code,

        #[Assert\Length(max: 255)]
        public ?string $logo = null,

        #[Assert\Length(max: 255)]
        public ?string $city = null,

        public ?Country $country = null,
        public ?Stadium $stadium = null,
    ) {}
}
