<?php

namespace App\Entity;

use App\Repository\HaikusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HaikusRepository::class)]
class Haikus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'haikus')]
    #[ORM\JoinColumn(name: "creator_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?User $creator = null;

    #[ORM\ManyToOne(inversedBy: 'haikus')]
    #[ORM\JoinColumn]
    private ?Collections $Collection = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, UserWords>
     */
    #[ORM\OneToMany(targetEntity: UserWords::class, mappedBy: 'haiku', cascade:['remove'], orphanRemoval: true)]
    private Collection $userWords;

    /**
     * @var Collection<int, Comments>
     */
    #[ORM\OneToMany(targetEntity: Comments::class, mappedBy: 'haiku', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, Likes>
     */
    #[ORM\OneToMany(targetEntity: Likes::class, mappedBy: 'haiku', orphanRemoval: true)]
    private Collection $likes;

    public function __construct()
    {
        $this->userWords = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    public function getCollection(): ?Collections
    {
        return $this->Collection;
    }

    public function setCollection(?Collections $Collection): static
    {
        $this->Collection = $Collection;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->Content;
    }

    public function setContent(string $Content): static
    {
        $this->Content = $Content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, UserWords>
     */
    public function getUserWords(): Collection
    {
        return $this->userWords;
    }

    public function addUserWord(UserWords $userWord): static
    {
        if (!$this->userWords->contains($userWord)) {
            $this->userWords->add($userWord);
            $userWord->setHaiku($this);
        }

        return $this;
    }

    public function removeUserWord(UserWords $userWord): static
    {
        if ($this->userWords->removeElement($userWord)) {
            // set the owning side to null (unless already changed)
            if ($userWord->getHaiku() === $this) {
                $userWord->setHaiku(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setHaiku($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getHaiku() === $this) {
                $comment->setHaiku(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Likes>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Likes $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setHaiku($this);
        }

        return $this;
    }

    // Affichage du nombre de like
    public function getLikesCount(): int {
        return $this->likes->count();
    }

    public function removeLike(Likes $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getHaiku() === $this) {
                $like->setHaiku(null);
            }
        }

        return $this;
    }

     public function __toString(): string
        {
            return $this->Content; 
        }
}
