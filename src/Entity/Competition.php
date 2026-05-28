<?php

namespace App\Entity;

use App\Repository\CompetitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompetitionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Competition
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $shortName = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $startYear = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $endYear = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    /**
     * @var Collection<int, CompetitionPhase>
     */
    #[ORM\OneToMany(targetEntity: CompetitionPhase::class, mappedBy: 'Competition', orphanRemoval: true, cascade: ['persist'])]
    #[ORM\OrderBy(['sequence' => 'ASC', 'name' => 'ASC'])]
    private Collection $competitionPhases;

    /**
     * @var Collection<int, Standing>
     */
    #[ORM\OneToMany(targetEntity: Standing::class, mappedBy: 'competition', orphanRemoval: true)]
    private Collection $standings;

    public function __construct()
    {
        $this->competitionPhases = new ArrayCollection();
        $this->standings = new ArrayCollection();
    }

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

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): static
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getStartYear(): ?int
    {
        return $this->startYear;
    }

    public function setStartYear(int $startYear): static
    {
        $this->startYear = $startYear;

        return $this;
    }

    public function getEndYear(): ?int
    {
        return $this->endYear;
    }

    public function setEndYear(int $endYear): static
    {
        $this->endYear = $endYear;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, CompetitionPhase>
     */
    public function getCompetitionPhases(): Collection
    {
        return $this->competitionPhases;
    }

    public function addCompetitionPhase(CompetitionPhase $competitionPhase): static
    {
        if (!$this->competitionPhases->contains($competitionPhase)) {
            $this->competitionPhases->add($competitionPhase);
            $competitionPhase->setCompetition($this);
        }

        return $this;
    }

    public function removeCompetitionPhase(CompetitionPhase $competitionPhase): static
    {
        if ($this->competitionPhases->removeElement($competitionPhase)) {
            // set the owning side to null (unless already changed)
            if ($competitionPhase->getCompetition() === $this) {
                $competitionPhase->setCompetition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Standing>
     */
    public function getStandings(): Collection
    {
        return $this->standings;
    }

    public function addStanding(Standing $standing): static
    {
        if (!$this->standings->contains($standing)) {
            $this->standings->add($standing);
            $standing->setCompetition($this);
        }

        return $this;
    }

    public function removeStanding(Standing $standing): static
    {
        if ($this->standings->removeElement($standing)) {
            // set the owning side to null (unless already changed)
            if ($standing->getCompetition() === $this) {
                $standing->setCompetition(null);
            }
        }

        return $this;
    }
}
