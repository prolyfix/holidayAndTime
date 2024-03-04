<?php

namespace App\Entity;

use App\Repository\UserPropertyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPropertyRepository::class)]
class UserProperty
{
    public function __construct()
    {
        $this->creationDate = new \DateTime();
    }
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userProperties')]
    private ?User $user = null;

    #[ORM\Column]
    private ?float $holidayPerYear = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
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

    public function getHolidayPerYear(): ?float
    {
        return $this->holidayPerYear;
    }

    public function setHolidayPerYear(float $holidayPerYear): static
    {
        $this->holidayPerYear = $holidayPerYear;

        return $this;
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
}
