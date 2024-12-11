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


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoName = null;

    /**
     * @var Collection<int, TypeOfAbsence>
     */
    #[ORM\OneToMany(targetEntity: TypeOfAbsence::class, mappedBy: 'company', cascade: ['persist', 'remove'])]
    private Collection $typeOfAbsences;

    /**
     * @var Collection<int, Dictionnary>
     */
    #[ORM\OneToMany(targetEntity: Dictionnary::class, mappedBy: 'company')]
    private Collection $dictionnaries;

    /**
     * @var Collection<int, CompanyValueConfiguration>
     */
    #[ORM\OneToMany(targetEntity: CompanyValueConfiguration::class, mappedBy: 'company')]
    private Collection $companyValueConfigurations;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->typeOfAbsences = new ArrayCollection();
        $this->dictionnaries = new ArrayCollection();
        $this->companyValueConfigurations = new ArrayCollection();
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

    /**
     * @return Collection<int, Dictionnary>
     */
    public function getDictionnaries(): Collection
    {
        return $this->dictionnaries;
    }

    public function addDictionnary(Dictionnary $dictionnary): static
    {
        if (!$this->dictionnaries->contains($dictionnary)) {
            $this->dictionnaries->add($dictionnary);
            $dictionnary->setCompany($this);
        }

        return $this;
    }

    public function removeDictionnary(Dictionnary $dictionnary): static
    {
        if ($this->dictionnaries->removeElement($dictionnary)) {
            // set the owning side to null (unless already changed)
            if ($dictionnary->getCompany() === $this) {
                $dictionnary->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CompanyValueConfiguration>
     */
    public function getCompanyValueConfigurations(): Collection
    {
        return $this->companyValueConfigurations;
    }

    public function addCompanyValueConfiguration(CompanyValueConfiguration $companyValueConfiguration): static
    {
        if (!$this->companyValueConfigurations->contains($companyValueConfiguration)) {
            $this->companyValueConfigurations->add($companyValueConfiguration);
            $companyValueConfiguration->setCompany($this);
        }

        return $this;
    }

    public function removeCompanyValueConfiguration(CompanyValueConfiguration $companyValueConfiguration): static
    {
        if ($this->companyValueConfigurations->removeElement($companyValueConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($companyValueConfiguration->getCompany() === $this) {
                $companyValueConfiguration->setCompany(null);
            }
        }

        return $this;
    }

}
