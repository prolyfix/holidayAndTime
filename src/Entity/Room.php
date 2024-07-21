<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Weekplan>
     */
    #[ORM\OneToMany(targetEntity: Weekplan::class, mappedBy: 'room')]
    private Collection $weekplans;

    public function __construct()
    {
        $this->weekplans = new ArrayCollection();
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

    /**
     * @return Collection<int, Weekplan>
     */
    public function getWeekplans(): Collection
    {
        return $this->weekplans;
    }

    public function addWeekplan(Weekplan $weekplan): static
    {
        if (!$this->weekplans->contains($weekplan)) {
            $this->weekplans->add($weekplan);
            $weekplan->setRoom($this);
        }

        return $this;
    }

    public function removeWeekplan(Weekplan $weekplan): static
    {
        if ($this->weekplans->removeElement($weekplan)) {
            // set the owning side to null (unless already changed)
            if ($weekplan->getRoom() === $this) {
                $weekplan->setRoom(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->name;
    }
}
