<?php

namespace App\Entity;

use App\Enum\PhaseTypeEnum;
use App\Repository\CompetitionPhaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompetitionPhaseRepository::class)]
#[ORM\HasLifecycleCallbacks]
class CompetitionPhase
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $sequence = null;

    #[ORM\Column(enumType: PhaseTypeEnum::class)]
    private ?PhaseTypeEnum $type = null;

    #[ORM\ManyToOne(inversedBy: 'competitionPhases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Competition $Competition = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setSequence(int $sequence): static
    {
        $this->sequence = $sequence;

        return $this;
    }

    public function getType(): ?PhaseTypeEnum
    {
        return $this->type;
    }

    public function setType(PhaseTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCompetition(): ?Competition
    {
        return $this->Competition;
    }

    public function setCompetition(?Competition $Competition): static
    {
        $this->Competition = $Competition;

        return $this;
    }
}
