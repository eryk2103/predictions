<?php

namespace App\Entity;

use App\Repository\PredictionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PredictionRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(fields: ['predictor', 'game'])]
class Prediction
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'predictions')]
    private ?User $predictor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $homeScore = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $awayScore = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $homePenalty = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $awayPenalty = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPredictor(): ?User
    {
        return $this->predictor;
    }

    public function setPredictor(?User $predictor): static
    {
        $this->predictor = $predictor;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getHomeScore(): ?int
    {
        return $this->homeScore;
    }

    public function setHomeScore(int $homeScore): static
    {
        $this->homeScore = $homeScore;

        return $this;
    }

    public function getAwayScore(): ?int
    {
        return $this->awayScore;
    }

    public function setAwayScore(int $awayScore): static
    {
        $this->awayScore = $awayScore;

        return $this;
    }

    public function getHomePenalty(): ?int
    {
        return $this->homePenalty;
    }

    public function setHomePenalty(?int $homePenalty): static
    {
        $this->homePenalty = $homePenalty;

        return $this;
    }

    public function getAwayPenalty(): ?int
    {
        return $this->awayPenalty;
    }

    public function setAwayPenalty(?int $awayPenalty): static
    {
        $this->awayPenalty = $awayPenalty;

        return $this;
    }
}
