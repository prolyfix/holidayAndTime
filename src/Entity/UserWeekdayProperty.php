<?php

namespace App\Entity;

use App\Repository\UserWeekdayPropertyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserWeekdayPropertyRepository::class)]
class UserWeekdayProperty
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userWeekdayProperties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $weekday = null;

    #[ORM\Column(nullable: true)]
    private ?float $workingDay = null;

    #[ORM\Column(nullable: true)]
    private ?float $workingHours = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $creationDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getWeekday(): ?string
    {
        return $this->weekday;
    }

    public function setWeekday(string $weekday): static
    {
        $this->weekday = $weekday;

        return $this;
    }

    public function getWorkingDay(): ?float
    {
        return $this->workingDay;
    }

    public function setWorkingDay(?float $workingDay): static
    {
        $this->workingDay = $workingDay;

        return $this;
    }

    public function getWorkingHours(): ?float
    {
        return $this->workingHours;
    }

    public function setWorkingHours(?float $workingHours): static
    {
        $this->workingHours = $workingHours;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }
}
