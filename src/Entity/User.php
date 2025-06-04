<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(length: 180, nullable: false)]
    private ?string $email = null;


    #[ORM\Column(length: 255, nullable: false)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: false)]
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

    /**
     * @var Collection<int, Comments>
     */
    #[ORM\OneToMany(targetEntity: Comments::class, mappedBy: 'sender', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, Likes>
     */
    #[ORM\OneToMany(targetEntity: Likes::class, mappedBy: 'sender', orphanRemoval: true)]
    private Collection $likes;

    /**
     * @var Collection<int, Notifications>
     */
    #[ORM\OneToMany(targetEntity: Notifications::class, mappedBy: 'Receiver', orphanRemoval: true)]
    private Collection $receivedNotif;

    /**
     * @var Collection<int, Notifications>
     */
    #[ORM\OneToMany(targetEntity: Notifications::class, mappedBy: 'Sender')]
    private Collection $sentNotif;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $resetTokenExpiresAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->follows = new ArrayCollection();
        $this->collections = new ArrayCollection();
        $this->haikus = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->receivedNotif = new ArrayCollection();
        $this->sentNotif = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
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
    

    public function getUsername(): ?string
    {
        return $this->username;
    }
    public function setUsername(string $username): static
    {
        $this->username = $username;
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
            $comment->setSender($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getSender() === $this) {
                $comment->setSender(null);
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
            $like->setSender($this);
        }

        return $this;
    }

    public function removeLike(Likes $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getSender() === $this) {
                $like->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notifications>
     */
    public function getReceivedNotif(): Collection
    {
        return $this->receivedNotif;
    }

    public function addReceivedNotif(Notifications $receivedNotif): static
    {
        if (!$this->receivedNotif->contains($receivedNotif)) {
            $this->receivedNotif->add($receivedNotif);
            $receivedNotif->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedNotif(Notifications $receivedNotif): static
    {
        if ($this->receivedNotif->removeElement($receivedNotif)) {
            // set the owning side to null (unless already changed)
            if ($receivedNotif->getReceiver() === $this) {
                $receivedNotif->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notifications>
     */
    public function getSentNotif(): Collection
    {
        return $this->sentNotif;
    }

    public function addSentNotif(Notifications $sentNotif): static
    {
        if (!$this->sentNotif->contains($sentNotif)) {
            $this->sentNotif->add($sentNotif);
            $sentNotif->setSender($this);
        }

        return $this;
    }

    public function removeSentNotif(Notifications $sentNotif): static
    {
        if ($this->sentNotif->removeElement($sentNotif)) {
            // set the owning side to null (unless already changed)
            if ($sentNotif->getSender() === $this) {
                $sentNotif->setSender(null);
            }
        }

        return $this;
    }

    public function __toString(): string
        {
            return $this->username; 
        }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getResetTokenExpiresAt(): ?\DateTimeImmutable
    {
        return $this->resetTokenExpiresAt;
    }

    public function setResetTokenExpiresAt(?\DateTimeInterface $resetTokenExpiresAt): static
    {
        $this->resetTokenExpiresAt = $resetTokenExpiresAt;

        return $this;
    }

    // Booléen pour vérifier si le token de réinitialisation de mdp est valide
    public function isResetTokenValid(): bool
    {
        return $this->resetTokenExpiresAt && $this->resetTokenExpiresAt > new \DateTime();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
