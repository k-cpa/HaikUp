<?php

namespace App\Service;

use App\Entity\EntityType;
use App\Entity\Haikus;
use App\Entity\Likes;
use App\Entity\Notifications;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class LikeService {

    private $entityManager;
    private $security;
    private $notificationService;

    public function __construct(EntityManagerInterface $entityManager, Security $security, NotificationService $notificationService) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->notificationService = $notificationService;
    }

    public function toggleLike(Haikus $haiku)
        {
            $user = $this->security->getUser();
            if (!$user) {
                throw new \Exception('Utilisateur non authentifié');
            }

            // Vérifier si le like existe déjà
            $existingLike = $this->entityManager->getRepository(Likes::class)->findOneBy([
                'sender' => $user,
                'haiku' => $haiku,
            ]);

            if ($existingLike) {
                // Suppression du like et de la notification associée
                $this->entityManager->remove($existingLike);

                // Récupérer l'entity type du like 
                $entityType = $this->entityManager->getRepository(EntityType::class)->findOneBy(['name' => 'like']); 
                // Récupérer ensuite la notif du like
                $notification = $this->entityManager->getRepository(Notifications::class)->findOneBy([
                    'Sender' => $user,
                    'Receiver' => $haiku->getCreator(),
                    'entity_type' => $entityType, 
                    'entity_id' => $haiku->getId(),
                ]);
                // Si on a une notif on supprime
                if ($notification) {
                    $this->entityManager->remove($notification);
                    $this->entityManager->flush();
                }
                $this->entityManager->flush();
                // affichage dynamique du like
                return false;
            } else {
                // Création d'un nouveau like 
                $this->addLike($haiku, $user);
                // affichage dynamique du like
                return true;
        }
    }

    private function addLike(Haikus $haiku, $user) {

                // Création du like
                $like = new Likes();
                $like->setSender($user);
                $like->setHaiku($haiku);
                $this->entityManager->persist($like);

                // Création de la notification
                $this->notificationService->createNotification(  // Utilisation service custom notif
                    $user,
                    $haiku->getCreator(),
                    'like',
                    $haiku->getId(),
                );

            $this->entityManager->flush(); 
    }

    public function isHaikuLikedByUser(Haikus $haiku, $user): bool {
        $existingLike = $this->entityManager->getRepository(Likes::class)->findOneBy([
            'sender' => $user,
            'haiku' => $haiku,
        ]);
        return $existingLike !== null;
    }
}
