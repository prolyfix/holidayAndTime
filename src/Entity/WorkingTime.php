<?php

namespace App\Entity;

use App\Repository\WorkingTimeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkingTimeRepository::class)]
class WorkingTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $Start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $EndTime = null;

    #[ORM\Column]
    private ?float $workinghours = null;

    #[ORM\Column(nullable: true)]
    private ?float $break = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->Start;
    }

    public function setStart(?\DateTimeInterface $Start): static
    {
        $this->Start = $Start;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->EndTime;
    }

    public function setEndTime(?\DateTimeInterface $EndTime): static
    {
        $this->EndTime = $EndTime;

        return $this;
    }

    public function getWorkinghours(): ?float
    {
        return $this->workinghours;
    }

    public function setWorkinghours(float $workinghours): static
    {
        $this->workinghours = $workinghours;

        return $this;
    }

    public function getBreak(): ?float
    {
        return $this->break;
    }

    public function setBreak(?float $break): static
    {
        $this->break = $break;

        return $this;
    }
}
