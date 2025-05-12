<?php

// Résumé
// > On met la logique métier dans un service pour :
// > - Garder les contrôleurs simples et lisibles
// > - Réutiliser la logique partout dans l’application
// > - Faciliter les tests
// > - Rendre le code plus maintenable et évolutif

namespace App\Service;

use App\Entity\EntityType;
use App\Entity\Notifications;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class NotificationService
{
    private $entityManager;

    // Injection de la dépendance dans la classe 
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager; 
    }

    // Méthode pour créer une notif
    public function createNotification(User $sender, User $receiver, string $entityType, int $entityId): void {

        // Vérification si les users existent 
        if(!$sender->getId()) {
            throw new InvalidArgumentException("Créateur de notification sans ID");
        }
        if(!$receiver->getId()) {
            throw new InvalidArgumentException("Receiver de notification sans ID");
        }

        // Recherche BDD un type d'entité par le nom
        $entityTypeEntity = $this->entityManager->getRepository(EntityType::class)->findOneBy(['name' => $entityType]);
        // Vérification entity Type existe
        if(!$entityTypeEntity) {
            throw new \Exception("Type d'entité non reconnu");
        }

        // Vérification entity ID
        if ($entityId <= 0) {
            throw new InvalidArgumentException("Entity ID inexistant");
        }

        // Créer une nouvelle notification on set tous les paramètres 
        $notification = new Notifications;
        $notification->setSender($sender); 
        $notification->setReceiver($receiver);
        $notification->setEntityType($entityTypeEntity);
        $notification->setEntityId($entityId);
        $notification->setStatus(false); // Notification marquée comme non lue par défault

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}


