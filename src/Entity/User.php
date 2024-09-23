<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User extends Commentable implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_MANAGER = 'ROLE_MANAGER';

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'users')]
    private ?self $manager = null;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'manager')]
    private Collection $users;


    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(targetEntity: UserProperty::class, mappedBy: 'user',cascade: ["persist"])]
    private Collection $userProperties;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?WorkingGroup $workingGroup = null;

    #[ORM\OneToMany(targetEntity: Calendar::class, mappedBy: 'user')]
    private Collection $calendars;

    #[ORM\OneToMany(targetEntity: Timesheet::class, mappedBy: 'user')]
    private Collection $timesheets;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable : true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Issue::class, mappedBy: 'user')]
    private Collection $issues;

    #[ORM\Column(nullable: true)]
    private ?bool $isDeactivated = null;

    #[ORM\Column(nullable: true)]
    private ?bool $hasTimesheet = null;

    /**
     * @var Collection<int, Weekplan>
     */
    #[ORM\OneToMany(targetEntity: Weekplan::class, mappedBy: 'user')]
    private Collection $weekplans;

    #[ORM\ManyToOne(inversedBy: 'users', cascade: ["persist"])]
    private ?Company $company = null;

    /**
     * @var Collection<int, UserSchedule>
     */
    #[ORM\OneToMany(targetEntity: UserSchedule::class, mappedBy: 'user', cascade: ["persist"])]
    private Collection $userSchedules;

    #[ORM\Column(nullable: true)]
    private ?bool $emailInteraction = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $state = null;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'assignedTo')]
    private Collection $tasks;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarFilename = null;

    /**
     * @var Collection<int, Commentable>
     */
    #[ORM\OneToMany(targetEntity: Commentable::class, mappedBy: 'createdBy')]
    private Collection $commentables;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'members')]
    private Collection $projects;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $color = null;



    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->userProperties = new ArrayCollection();
        $this->calendars = new ArrayCollection();
        $this->timesheets = new ArrayCollection();
        $this->issues = new ArrayCollection();
        $this->userSchedules = new ArrayCollection();
        $timestamp = strtotime('next Monday');
        $userSchedule = (new UserSchedule())->setUser($this)->setEffectiveDate(new \DateTime());
        $this->addUserSchedule($userSchedule);
        for ($i = 0; $i < 7; $i++) {
            $userWeekdayProperty = new UserWeekdayProperty();
            $userWeekdayProperty->setUserSchedule($userSchedule);
            $userWeekdayProperty->setWeekday(strftime('%A', $timestamp));
            $timestamp = strtotime('+1 day', $timestamp);
            $userSchedule->addUserWeekdayProperty($userWeekdayProperty);
        }
        $userProperty = (new UserProperty())->setHolidayPerYear(0);
        $this->addUserProperty($userProperty);
        $this->weekplans = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->commentables = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getManager(): ?self
    {
        return $this->manager;
    }

    public function setManager(?self $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(self $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setManager($this);
        }

        return $this;
    }

    public function removeUser(self $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getManager() === $this) {
                $user->setManager(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }


    /**
     * @return Collection<int, UserProperty>
     */
    public function getUserProperties(): Collection
    {
        return $this->userProperties;
    }

    public function addUserProperty(UserProperty $userProperty): static
    {
        if (!$this->userProperties->contains($userProperty)) {
            $this->userProperties->add($userProperty);
            $userProperty->setUser($this);
        }

        return $this;
    }

    public function removeUserProperty(UserProperty $userProperty): static
    {
        if ($this->userProperties->removeElement($userProperty)) {
            // set the owning side to null (unless already changed)
            if ($userProperty->getUser() === $this) {
                $userProperty->setUser(null);
            }
        }

        return $this;
    }

    public function getWorkingGroup(): ?WorkingGroup
    {
        return $this->workingGroup;
    }

    public function setWorkingGroup(?WorkingGroup $workingGroup): static
    {
        $this->workingGroup = $workingGroup;

        return $this;
    }

    /**
     * @return Collection<int, Calendar>
     */
    public function getCalendars(): Collection
    {
        return $this->calendars;
    }

    public function addCalendar(Calendar $calendar): static
    {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars->add($calendar);
            $calendar->setUser($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): static
    {
        if ($this->calendars->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getUser() === $this) {
                $calendar->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Timesheet>
     */
    public function getTimesheets(): Collection
    {
        return $this->timesheets;
    }

    public function addTimesheet(Timesheet $timesheet): static
    {
        if (!$this->timesheets->contains($timesheet)) {
            $this->timesheets->add($timesheet);
            $timesheet->setUser($this);
        }

        return $this;
    }

    public function removeTimesheet(Timesheet $timesheet): static
    {
        if ($this->timesheets->removeElement($timesheet)) {
            // set the owning side to null (unless already changed)
            if ($timesheet->getUser() === $this) {
                $timesheet->setUser(null);
            }
        }

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name??"NO NAME";
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Issue>
     */
    public function getIssues(): Collection
    {
        return $this->issues;
    }

    public function addIssue(Issue $issue): static
    {
        if (!$this->issues->contains($issue)) {
            $this->issues->add($issue);
            $issue->setUser($this);
        }

        return $this;
    }

    public function removeIssue(Issue $issue): static
    {
        if ($this->issues->removeElement($issue)) {
            // set the owning side to null (unless already changed)
            if ($issue->getUser() === $this) {
                $issue->setUser(null);
            }
        }

        return $this;

    }

    public function hasRole($role): bool
    {
        return in_array($role, $this->roles);
    }

    public function __toString() {
        // Assuming the User class has a 'name' property
        return $this->name??$this->email??"";
    }

    public function isIsDeactivated(): ?bool
    {
        return $this->isDeactivated;
    }

    public function setIsDeactivated(?bool $isDeactivated): static
    {
        $this->isDeactivated = $isDeactivated;

        return $this;
    }

    public function isHasTimesheet(): ?bool
    {
        return $this->hasTimesheet;
    }

    public function setHasTimesheet(?bool $hasTimesheet): static
    {
        $this->hasTimesheet = $hasTimesheet;

        return $this;
    }

    public function isCustomWorkday($date ): bool
    {
        foreach($this->getCalendars() as $calendar){
            if($calendar->getTypeOfAbsence()->isIsWorkingDay() && $calendar->getStartDate() <= $date && $calendar->getEndDate() >= $date){
                return true;
            }

        }
        return false;
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
            $weekplan->setUser($this);
        }

        return $this;
    }

    public function removeWeekplan(Weekplan $weekplan): static
    {
        if ($this->weekplans->removeElement($weekplan)) {
            // set the owning side to null (unless already changed)
            if ($weekplan->getUser() === $this) {
                $weekplan->setUser(null);
            }
        }

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, UserSchedule>
     */
    public function getUserSchedules(): Collection
    {
        return $this->userSchedules;
    }

    public function addUserSchedule(UserSchedule $userSchedule): static
    {
        if (!$this->userSchedules->contains($userSchedule)) {
            $this->userSchedules->add($userSchedule);
            $userSchedule->setUser($this);
        }

        return $this;
    }

    public function removeUserSchedule(UserSchedule $userSchedule): static
    {
        if ($this->userSchedules->removeElement($userSchedule)) {
            // set the owning side to null (unless already changed)
            if ($userSchedule->getUser() === $this) {
                $userSchedule->setUser(null);
            }
        }

        return $this;
    }

    public function getRightUserWeekdayProperties(DateTime $dateTime): iterable
    {
        $userSchedules = $this->getUserSchedules();
        $finalChoosen = [];
        $intervalInDays = 10000000000000;
        foreach($userSchedules as $userSchedule){
            if($userSchedule->getEffectiveDate() <= $dateTime && $dateTime->diff($userSchedule->getEffectiveDate())->days < $intervalInDays){
                $intervalInDays = $dateTime->diff($userSchedule->getEffectiveDate())->days;
                $finalChoosen = $userSchedule->getUserWeekdayProperties();
            }
        }
        return $finalChoosen;
    }

    public function isEmailInteraction(): ?bool
    {
        return $this->emailInteraction;
    }

    public function setEmailInteraction(?bool $emailInteraction): static
    {
        $this->emailInteraction = $emailInteraction;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setAssignedTo($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getAssignedTo() === $this) {
                $task->setAssignedTo(null);
            }
        }

        return $this;
    }

    public function getAvatarFilename(): ?string
    {
        return $this->avatarFilename;
    }

    public function setAvatarFilename(?string $avatarFilename): static
    {
        $this->avatarFilename = $avatarFilename;

        return $this;
    }

    /**
     * @return Collection<int, Commentable>
     */
    public function getCommentables(): Collection
    {
        return $this->commentables;
    }

    public function addCommentable(Commentable $commentable): static
    {
        if (!$this->commentables->contains($commentable)) {
            $this->commentables->add($commentable);
            $commentable->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCommentable(Commentable $commentable): static
    {
        if ($this->commentables->removeElement($commentable)) {
            // set the owning side to null (unless already changed)
            if ($commentable->getCreatedBy() === $this) {
                $commentable->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addMember($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            $project->removeMember($this);
        }

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, FrontendWidget>
     */
    public function getFrontendWidgets(): Collection
    {
        return $this->frontendWidgets;
    }



}
