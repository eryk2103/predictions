<?php

namespace App\Entity;

use App\Repository\StandingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StandingRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Standing
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'standings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Competition $competition = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $position = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $played = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $won = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $drawn = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $lost = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $goals = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $goalsAgainst = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $points = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    public function setCompetition(?Competition $competition): static
    {
        $this->competition = $competition;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getPlayed(): ?int
    {
        return $this->played;
    }

    public function setPlayed(int $played): static
    {
        $this->played = $played;

        return $this;
    }

    public function getWon(): ?int
    {
        return $this->won;
    }

    public function setWon(int $won): static
    {
        $this->won = $won;

        return $this;
    }

    public function getDrawn(): ?int
    {
        return $this->drawn;
    }

    public function setDrawn(int $drawn): static
    {
        $this->drawn = $drawn;

        return $this;
    }

    public function getLost(): ?int
    {
        return $this->lost;
    }

    public function setLost(int $lost): static
    {
        $this->lost = $lost;

        return $this;
    }

    public function getGoals(): ?int
    {
        return $this->goals;
    }

    public function setGoals(int $goals): static
    {
        $this->goals = $goals;

        return $this;
    }

    public function getGoalsAgainst(): ?int
    {
        return $this->goalsAgainst;
    }

    public function setGoalsAgainst(int $goalsAgainst): static
    {
        $this->goalsAgainst = $goalsAgainst;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }
}
