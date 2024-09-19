<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company extends Commentable
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'company')]
    private Collection $projects;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'company')]
    private Collection $tasks;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'company')]
    private Collection $users;

    #[ORM\Column(nullable: true)]
    private ?bool $isTutorialDone = null;

    #[ORM\OneToOne(inversedBy: 'company', cascade: ['persist', 'remove'])]
    private ?Location $location = null;

    /**
     * @var Collection<int, Configuration>
     */
    #[ORM\OneToMany(targetEntity: Configuration::class, mappedBy: 'company', cascade: ['persist', 'remove'])]
    private Collection $configurations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoName = null;

    /**
     * @var Collection<int, TypeOfAbsence>
     */
    #[ORM\OneToMany(targetEntity: TypeOfAbsence::class, mappedBy: 'company', cascade: ['persist', 'remove'])]
    private Collection $typeOfAbsences;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->configurations = new ArrayCollection();
        $this->typeOfAbsences = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name ?? '';
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

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
            $project->setCompany($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getCompany() === $this) {
                $project->setCompany(null);
            }
        }

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
            $task->setCompany($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getCompany() === $this) {
                $task->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
            }
        }

        return $this;
    }

    public function isTutorialDone(): ?bool
    {
        return $this->isTutorialDone;
    }

    public function setTutorialDone(?bool $isTutorialDone): static
    {
        $this->isTutorialDone = $isTutorialDone;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, Configuration>
     */
    public function getConfigurations(): Collection
    {
        return $this->configurations;
    }

    public function getConfiguration(string $key): ?Configuration
    {
        foreach ($this->configurations as $configuration) {
            if ($configuration->getName() === $key) {
                return $configuration;
            }
        }
        return null;
    }

    public function addConfiguration(Configuration $configuration): static
    {
        if (!$this->configurations->contains($configuration)) {
            $this->configurations->add($configuration);
            $configuration->setCompany($this);
        }

        return $this;
    }

    public function removeConfiguration(Configuration $configuration): static
    {
        if ($this->configurations->removeElement($configuration)) {
            // set the owning side to null (unless already changed)
            if ($configuration->getCompany() === $this) {
                $configuration->setCompany(null);
            }
        }

        return $this;
    }

    public function getLogoName(): ?string
    {
        return $this->logoName;
    }

    public function setLogoName(?string $logoName): static
    {
        $this->logoName = $logoName;

        return $this;
    }

    /**
     * @return Collection<int, TypeOfAbsence>
     */
    public function getTypeOfAbsences(): Collection
    {
        return $this->typeOfAbsences;
    }

    public function addTypeOfAbsence(TypeOfAbsence $typeOfAbsence): static
    {
        if (!$this->typeOfAbsences->contains($typeOfAbsence)) {
            $this->typeOfAbsences->add($typeOfAbsence);
            $typeOfAbsence->setCompany($this);
        }

        return $this;
    }

    public function removeTypeOfAbsence(TypeOfAbsence $typeOfAbsence): static
    {
        if ($this->typeOfAbsences->removeElement($typeOfAbsence)) {
            // set the owning side to null (unless already changed)
            if ($typeOfAbsence->getCompany() === $this) {
                $typeOfAbsence->setCompany(null);
            }
        }

        return $this;
    }
}
