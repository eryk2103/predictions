<?php

namespace App\Dto;

use App\Entity\Country;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateStadiumDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $name,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $city,

        #[Assert\Positive]
        public ?int $capacity = null,

        public ?Country $country = null,
    ) {}
}
