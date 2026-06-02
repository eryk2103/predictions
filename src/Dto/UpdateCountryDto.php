<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateCountryDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $name,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $flag,
    ) {}
}
