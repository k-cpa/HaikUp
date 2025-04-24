<?php

namespace App\Entity;

use App\Repository\CollectionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectionsRepository::class)]
class Collections
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'collections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, Haikus>
     */
    #[ORM\OneToMany(targetEntity: Haikus::class, mappedBy: 'Collection')]
    private Collection $haikus;

    public function __construct()
    {
        $this->haikus = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
     * @return Collection<int, Haikus>
     */
    public function getHaikus(): Collection
    {
        return $this->haikus;
    }

    public function addHaiku(Haikus $haiku): static
    {
        if (!$this->haikus->contains($haiku)) {
            $this->haikus->add($haiku);
            $haiku->setCollection($this);
        }

        return $this;
    }

    public function removeHaiku(Haikus $haiku): static
    {
        if ($this->haikus->removeElement($haiku)) {
            // set the owning side to null (unless already changed)
            if ($haiku->getCollection() === $this) {
                $haiku->setCollection(null);
            }
        }

        return $this;
    }
}
