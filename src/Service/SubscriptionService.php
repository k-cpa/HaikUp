<?php

namespace App\Service;

use App\Entity\Follows;
use App\Entity\User;
use App\Repository\FollowsRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionService {

    private EntityManagerInterface $entityManager;
    private FollowsRepository $followsRepository;

    public function __construct(FollowsRepository $followsRepository, EntityManagerInterface $entityManager) {
        $this->followsRepository = $followsRepository;
        $this->entityManager = $entityManager;
    }

    public function toggleSubscription(User $sender, User $receiver): bool
    {
        if ($sender->getId() === $receiver->getId()) {
            return false;
        }

        $existing = $this->followsRepository->findOneBy([
            'Sender' => $sender,
            'Receiver' => $receiver
        ]);

        if ($existing) {
            $this->entityManager->remove($existing);
            $this->entityManager->flush();
            return false; // Désabonné
        }

        $subscription = new Follows();
        $subscription->setSender($sender);
        $subscription->setReceiver($receiver);
        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        return true; // Abonné
    }
}