<?php

namespace App\Entity;

use App\Entity\Trait\TimestampTrait;
use App\Enum\GameStatusEnum;
use App\Repository\GameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Game
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    private ?Team $homeTeam = null;

    #[ORM\ManyToOne(inversedBy: 'awayGames')]
    private ?Team $awayTeam = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $homeScore = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $awayScore = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $homePenaltyScore = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $awayPenaltyScore = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?CompetitionPhase $competitionPhase = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    private ?Stadium $stadium = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $datetime = null;

    #[ORM\Column(enumType: GameStatusEnum::class)]
    #[Assert\NotNull]
    private ?GameStatusEnum $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHomeTeam(): ?Team
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(?Team $homeTeam): static
    {
        $this->homeTeam = $homeTeam;

        return $this;
    }

    public function getAwayTeam(): ?Team
    {
        return $this->awayTeam;
    }

    public function setAwayTeam(?Team $awayTeam): static
    {
        $this->awayTeam = $awayTeam;

        return $this;
    }

    public function getHomeScore(): ?int
    {
        return $this->homeScore;
    }

    public function setHomeScore(?int $homeScore): static
    {
        $this->homeScore = $homeScore;

        return $this;
    }

    public function getAwayScore(): ?int
    {
        return $this->awayScore;
    }

    public function setAwayScore(?int $awayScore): static
    {
        $this->awayScore = $awayScore;

        return $this;
    }

    public function getHomePenaltyScore(): ?int
    {
        return $this->homePenaltyScore;
    }

    public function setHomePenaltyScore(?int $homePenaltyScore): static
    {
        $this->homePenaltyScore = $homePenaltyScore;

        return $this;
    }

    public function getAwayPenaltyScore(): ?int
    {
        return $this->awayPenaltyScore;
    }

    public function setAwayPenaltyScore(?int $awayPenaltyScore): static
    {
        $this->awayPenaltyScore = $awayPenaltyScore;

        return $this;
    }

    public function getCompetitionPhase(): ?CompetitionPhase
    {
        return $this->competitionPhase;
    }

    public function setCompetitionPhase(?CompetitionPhase $competitionPhase): static
    {
        $this->competitionPhase = $competitionPhase;

        return $this;
    }

    public function getStadium(): ?Stadium
    {
        return $this->stadium;
    }

    public function setStadium(?Stadium $stadium): static
    {
        $this->stadium = $stadium;

        return $this;
    }

    public function getDatetime(): ?\DateTime
    {
        return $this->datetime;
    }

    public function setDatetime(?\DateTime $datetime): static
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getStatus(): ?GameStatusEnum
    {
        return $this->status;
    }

    public function setStatus(?GameStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }
}
