<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Country
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $flag = null;

    /**
     * @var Collection<int, Stadium>
     */
    #[ORM\OneToMany(targetEntity: Stadium::class, mappedBy: 'country')]
    private Collection $stadiums;

    public function __construct()
    {
        $this->stadiums = new ArrayCollection();
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

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setFlag(string $flag): static
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * @return Collection<int, Stadium>
     */
    public function getStadiums(): Collection
    {
        return $this->stadiums;
    }

    public function addStadium(Stadium $stadium): static
    {
        if (!$this->stadiums->contains($stadium)) {
            $this->stadiums->add($stadium);
            $stadium->setCountry($this);
        }

        return $this;
    }

    public function removeStadium(Stadium $stadium): static
    {
        if ($this->stadiums->removeElement($stadium)) {
            // set the owning side to null (unless already changed)
            if ($stadium->getCountry() === $this) {
                $stadium->setCountry(null);
            }
        }

        return $this;
    }
}
