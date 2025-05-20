<?php

namespace App\Entity;

use App\Repository\UserWordsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserWordsRepository::class)]
class UserWords
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn]
    private ?User $Receiver = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Words $Words = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'userWords')]
    private ?Haikus $haiku = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->Receiver;
    }

    public function setReceiver(?User $Receiver): static
    {
        $this->Receiver = $Receiver;

        return $this;
    }

    public function getWords(): ?Words
    {
        return $this->Words;
    }

    public function setWords(?Words $Words): static
    {
        $this->Words = $Words;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getHaiku(): ?Haikus
    {
        return $this->haiku;
    }

    public function setHaiku(?Haikus $haiku): static
    {
        $this->haiku = $haiku;

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

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }
}
