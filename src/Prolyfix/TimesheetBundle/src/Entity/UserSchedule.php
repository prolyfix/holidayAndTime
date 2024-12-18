<?php

namespace App\Entity;

use App\Repository\UserScheduleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserScheduleRepository::class)]
class UserSchedule extends Commentable
{
    #[ORM\ManyToOne(inversedBy: 'userSchedules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $effectiveDate = null;

    /**
     * @var Collection<int, UserWeekdayProperty>
     */
    #[ORM\OneToMany(targetEntity: UserWeekdayProperty::class, mappedBy: 'userSchedule', cascade: ['persist'], fetch: 'EAGER')]
    private Collection $userWeekdayProperties;

    public function __construct()
    {
        parent::__construct();
        $this->userWeekdayProperties = new ArrayCollection();
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

    public function getEffectiveDate(): ?\DateTimeInterface
    {
        return $this->effectiveDate;
    }

    public function setEffectiveDate(?\DateTimeInterface $effectiveDate): static
    {
        $this->effectiveDate = $effectiveDate;

        return $this;
    }

    /**
     * @return Collection<int, UserWeekdayProperty>
     */
    public function getUserWeekdayProperties(): Collection
    {
        return $this->userWeekdayProperties;
    }

    public function addUserWeekdayProperty(UserWeekdayProperty $userWeekdayProperty): static
    {
        if (!$this->userWeekdayProperties->contains($userWeekdayProperty)) {
            $this->userWeekdayProperties->add($userWeekdayProperty);
            $userWeekdayProperty->setUserSchedule($this);
        }

        return $this;
    }

    public function removeUserWeekdayProperty(UserWeekdayProperty $userWeekdayProperty): static
    {
        if ($this->userWeekdayProperties->removeElement($userWeekdayProperty)) {
            // set the owning side to null (unless already changed)
            if ($userWeekdayProperty->getUserSchedule() === $this) {
                $userWeekdayProperty->setUserSchedule(null);
            }
        }

        return $this;
    }

    public function __tostring(){
        return 'UserSchedule';
    }
}
