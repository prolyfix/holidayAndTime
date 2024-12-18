<?php

namespace Prolyfix\TimesheetBundle\Entity;

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

    #[ORM\Column(length: 255)]
    private ?string $weekday = null;

    #[ORM\Column(nullable: true)]
    private ?float $workingDay = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $workingHours = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\ManyToOne(inversedBy: 'userWeekdayProperties', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserSchedule $userSchedule = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->getWeekday();
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

    public function getWorkingHours(): ?\DateTimeInterface
    {
        return $this->workingHours;
    }

    public function setWorkingHours(?\DateTimeInterface $workingHours): static
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

    public function getUserSchedule(): ?UserSchedule
    {
        return $this->userSchedule;
    }

    public function setUserSchedule(?UserSchedule $userSchedule): static
    {
        $this->userSchedule = $userSchedule;

        return $this;
    }


}
