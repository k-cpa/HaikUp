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
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\ManyToOne(inversedBy: 'haikus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Collections $Collection = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, UserWords>
     */
    #[ORM\OneToMany(targetEntity: UserWords::class, mappedBy: 'haiku')]
    private Collection $userWords;

    public function __construct()
    {
        $this->userWords = new ArrayCollection();
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
}
