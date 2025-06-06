<?php

namespace App\Service;

use App\Entity\Follows;
use App\Entity\User;
use App\Repository\EntityTypeRepository;
use App\Repository\FollowsRepository;
use App\Repository\NotificationsRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionService {

    private EntityManagerInterface $entityManager;
    private FollowsRepository $followsRepository;
    private NotificationService $notificationService;
    private NotificationsRepository $notificationRepository;
    private EntityTypeRepository $entityTypeRepo;

    public function __construct(FollowsRepository $followsRepository, EntityManagerInterface $entityManager, NotificationService $notificationService, NotificationsRepository $notificationRepository, EntityTypeRepository $entityTypeRepo) {
        $this->followsRepository = $followsRepository;
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
        $this->notificationRepository = $notificationRepository;
        $this->entityTypeRepo = $entityTypeRepo;
    }

    public function toggleSubscription(User $sender, User $receiver): bool
    {
        $entityType = $this->entityTypeRepo->findOneBy(['name' => 'follow']);


        if ($sender->getId() === $receiver->getId()) {
            return false;
        }

        $existing = $this->followsRepository->findOneBy([
            'Sender' => $sender,
            'Receiver' => $receiver
        ]);

        // Si on a déjà un follow alors on va le supprimer et supprimer la notif associée
        if ($existing) {
            $notification = $this->notificationRepository->findOneBy([
                'Sender' => $sender,
                'Receiver' => $receiver,
                'entity_type' => $entityType,
                'entity_id' => $existing->getId(),
            ]);
            $this->entityManager->remove($existing);
            $this->entityManager->remove($notification);
            $this->entityManager->flush();
            return false; // Désabonné
        }

        $subscription = new Follows();
        $subscription->setSender($sender);
        $subscription->setReceiver($receiver);
        $this->entityManager->persist($subscription);

        // Un premier flush obligatoire pour récupérer l'id du follow -> optimisation possible ?
        $this->entityManager->flush();

        // Création de la notification
        $this->notificationService->createNotification(
            $sender,
            $receiver,
            'follow',
            $subscription->getId(),
        );

        $this->entityManager->flush();

        return true; // Abonné
    }
}