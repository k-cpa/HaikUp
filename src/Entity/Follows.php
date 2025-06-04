<?php

namespace App\Entity;

use App\Repository\FollowsRepository;
use Doctrine\ORM\Mapping as ORM;
// use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: FollowsRepository::class)]
// #[Broadcast(false)]
class Follows
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $Sender = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $Receiver = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->Sender;
    }

    public function setSender(?User $Sender): static
    {
        $this->Sender = $Sender;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $Created_at): static
    {
        $this->created_at = $Created_at;

        return $this;
    }

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }
}
