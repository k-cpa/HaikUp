<?php

namespace App\Service;

use App\Entity\Follows;
use App\Entity\User;
use App\Repository\FollowsRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionService {

    private EntityManagerInterface $entityManager;
    private FollowsRepository $followsRepository;

    public function toggleSubscription(User $sender, User $receiver): bool
    {
        if ($sender === $receiver) {
            return false;
        }

        $existing = $this->followsRepository->findOneBy([
            'sender' => $sender,
            'target' => $receiver
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