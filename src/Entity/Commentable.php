<?php

namespace App\Entity;

use App\Repository\CommentableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;

#[ORM\Entity(repositoryClass: CommentableRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    'company' => Company::class, 
    'user' => User::class, 
    'location' => Location::class, 
    'calendar' => Calendar::class, 
    'userSchedule' => UserSchedule::class, 
    'project' => Project::class,
    'workingGroup' => WorkingGroup::class,
    'task' => Task::class])]  
abstract class Commentable extends TimeData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'commentable', cascade: ['persist'])]
    private Collection $comments;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'commentable')]
    private Collection $media;


    #[ORM\ManyToOne(inversedBy: 'commentables')]
    private ?User $createdBy = null;

    /**
     * @var Collection<int, Timesheet>
     */
    #[ORM\OneToMany(targetEntity: Timesheet::class, mappedBy: 'relatedCommentable')]
    private Collection $relatedTimesheets;



    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->relatedTimesheets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setCommentable($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getCommentable() === $this) {
                $comment->setCommentable(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setCommentable($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getCommentable() === $this) {
                $medium->setCommentable(null);
            }
        }

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, Timesheet>
     */
    public function getRelatedTimesheets(): Collection
    {
        return $this->relatedTimesheets;
    }

    public function addRelatedTimesheet(Timesheet $relatedTimesheet): static
    {
        if (!$this->relatedTimesheets->contains($relatedTimesheet)) {
            $this->relatedTimesheets->add($relatedTimesheet);
            $relatedTimesheet->setRelatedCommentable($this);
        }

        return $this;
    }

    public function removeRelatedTimesheet(Timesheet $relatedTimesheet): static
    {
        if ($this->relatedTimesheets->removeElement($relatedTimesheet)) {
            // set the owning side to null (unless already changed)
            if ($relatedTimesheet->getRelatedCommentable() === $this) {
                $relatedTimesheet->setRelatedCommentable(null);
            }
        }

        return $this;
    }

}
