<?php

namespace App\Entity;

use App\Repository\HoldaysRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HoldaysRepository::class)]
class Holdays
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(nullable: true)]
    private ?float $holidayPerYear = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getHolidayPerYear(): ?float
    {
        return $this->holidayPerYear;
    }

    public function setHolidayPerYear(?float $holidayPerYear): static
    {
        $this->holidayPerYear = $holidayPerYear;

        return $this;
    }
}
