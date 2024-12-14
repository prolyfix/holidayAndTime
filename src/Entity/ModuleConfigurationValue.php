<?php

namespace App\Entity;

use App\Repository\ModuleConfigurationValueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleConfigurationValueRepository::class)]
class ModuleConfigurationValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'moduleConfigurationValues')]
    private ?ModuleConfiguration $moduleConfiguration = null;

    #[ORM\Column(nullable: true)]
    private ?int $relatedId = null;

    #[ORM\Column(length: 255)]
    private ?string $relatedClass = null;

    #[ORM\Column(nullable: true)]
    private ?array $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModuleConfiguration(): ?ModuleConfiguration
    {
        return $this->moduleConfiguration;
    }

    public function setModuleConfiguration(?ModuleConfiguration $moduleConfiguration): static
    {
        $this->moduleConfiguration = $moduleConfiguration;

        return $this;
    }

    public function getRelatedId(): ?int
    {
        return $this->relatedId;
    }

    public function setRelatedId(?int $relatedId): static
    {
        $this->relatedId = $relatedId;

        return $this;
    }


    public function getRelatedClass(): ?string
    {
        return $this->relatedClass;
    }

    public function setRelatedClass(string $relatedClass): static
    {
        $this->relatedClass = $relatedClass;

        return $this;
    }

    public function getValue() 
    {
        if(count($this->value) == 0)
        {
            return null;
        }
        if(count($this->value) == 1)
        {
            return $this->value[0];
        }
        return $this->value;
    }

    public function setValue(?array $value): static
    {
        $this->value = $value;

        return $this;
    }

}
