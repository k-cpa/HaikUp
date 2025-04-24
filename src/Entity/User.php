<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
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

    /**
     * @var Collection<int, Follows>
     */
    #[ORM\OneToMany(targetEntity: Follows::class, mappedBy: 'Sender', orphanRemoval: true)]
    private Collection $follows;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, Collections>
     */
    #[ORM\OneToMany(targetEntity: Collections::class, mappedBy: 'creator', orphanRemoval: true)]
    private Collection $collections;

    /**
     * @var Collection<int, Haikus>
     */
    #[ORM\OneToMany(targetEntity: Haikus::class, mappedBy: 'creator', orphanRemoval: true)]
    private Collection $haikus;

    public function __construct()
    {
        $this->follows = new ArrayCollection();
        $this->collections = new ArrayCollection();
        $this->haikus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
    public function getPassword(): ?string
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

    /**
     * @return Collection<int, Follows>
     */
    public function getFollows(): Collection
    {
        return $this->follows;
    }

    public function addFollow(Follows $follow): static
    {
        if (!$this->follows->contains($follow)) {
            $this->follows->add($follow);
            $follow->setSender($this);
        }

        return $this;
    }

    public function removeFollow(Follows $follow): static
    {
        if ($this->follows->removeElement($follow)) {
            // set the owning side to null (unless already changed)
            if ($follow->getSender() === $this) {
                $follow->setSender(null);
            }
        }

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
     * @return Collection<int, Collections>
     */
    public function getCollections(): Collection
    {
        return $this->collections;
    }

    public function addCollection(Collections $collection): static
    {
        if (!$this->collections->contains($collection)) {
            $this->collections->add($collection);
            $collection->setCreator($this);
        }

        return $this;
    }

    public function removeCollection(Collections $collection): static
    {
        if ($this->collections->removeElement($collection)) {
            // set the owning side to null (unless already changed)
            if ($collection->getCreator() === $this) {
                $collection->setCreator(null);
            }
        }

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
            $haiku->setCreator($this);
        }

        return $this;
    }

    public function removeHaiku(Haikus $haiku): static
    {
        if ($this->haikus->removeElement($haiku)) {
            // set the owning side to null (unless already changed)
            if ($haiku->getCreator() === $this) {
                $haiku->setCreator(null);
            }
        }

        return $this;
    }
}
