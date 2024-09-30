<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $color = null;

    /**
     * @var Collection<int, Commentable>
     */
    #[ORM\ManyToMany(targetEntity: Commentable::class, inversedBy: 'tags')]
    private Collection $commentables;

    public function __construct()
    {
        $this->commentables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
        }

        return $this;
    }

    public function removeCommentable(Commentable $commentable): static
    {
        $this->commentables->removeElement($commentable);

        return $this;
    }
}
