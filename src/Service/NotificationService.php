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

        // pas de création de notification si le user like son propre texte ou répond à un commentaire sur son haïku
        if ($sender->getId() === $receiver->getId()) {
            return;
        }

        // Vérification si les users existent 
        if(!$sender->getId()) {
            throw new InvalidArgumentException("Créateur de notification sans ID");
        }
        if(!$receiver->getId()) {
            throw new InvalidArgumentException("Receiver de notification sans ID");
        }

        // Recherche BDD un type d'entité par le nom
        $entityTypeEntity = $this->entityManager->getRepository(EntityType::class)->findOneBy(['name' => $entityType]);
        
        // Si le type n'existe pas encore, on le crée -> pour la première création 
        if (!$entityTypeEntity) {
            // Créer un nouvel EntityType
            $entityTypeEntity = new EntityType();
            $entityTypeEntity->setName($entityType);
            $this->entityManager->persist($entityTypeEntity);
            $this->entityManager->flush(); 
            
        }

        // Vérification entity ID
        if ($entityId <= 0) {
            throw new InvalidArgumentException("Entity ID inexistant");
        }

//         Gestion du message de la notification
// sprintf -> fonction PHP pour formater une chaine de caractères en y insérant des valeurs dynamiques. 
// -> premier argument = chaine contenant placeholders et ensuite autant d'arguments que de placeholders pour remplacement dynamique.
        $message= '';
        // On récup le nom de l'entité
        $entityTypeName = $entityTypeEntity->getName(); 
        switch ($entityTypeName) {
            case 'like':
                $message = sprintf("%s a liké votre haïku.", $sender->getUsername());
                break;

            case 'follow':
                $message = sprintf("%s vous suit.", $sender->getUsername());
                break;

            case 'comment':
                $message = sprintf("%s a commenté votre haïku.", $sender->getUsername());
                break;

            default:
                $message = sprintf("Découvrez votre nouvelle notification");

        }

        // Créer une nouvelle notification on set tous les paramètres 
        $notification = new Notifications;
        $notification->setSender($sender); 
        $notification->setReceiver($receiver);
        $notification->setEntityType($entityTypeEntity);
        $notification->setEntityId($entityId);
        $notification->setMessage($message);
        $notification->setStatus(false); // Notification marquée comme non lue par défault

        $this->entityManager->persist($notification);
        // $this->entityManager->flush(); on va plutôt flush dans les controller
    }
}


