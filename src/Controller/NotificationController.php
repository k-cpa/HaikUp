<?php 

namespace App\Controller;

use App\Entity\User;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{

    private $notificationService;
    private $entityManager;

    // Injection du service custom 
    public function __construct(NotificationService $notificationService, EntityManagerInterface $entityManager) 
    {
        $this->notificationService = $notificationService;
        $this->entityManager = $entityManager; // On injecte ici l'EntityManager
    }

    #[Route('/test-notification/{receiverId}', name: 'test_notification')]
    public function testNotification(int $receiverId, Security $security): JsonResponse {

        // Exemple de création notif 
        $sender = $security->getUser();
        if(!$sender) {
            return $this->json(['error' => 'Utilisateur non authent', 401]);
        }
        $receiver = $this->entityManager->getRepository(User::class)->find($receiverId);

        if(!$receiver) {
            return $this->json(['error' => 'User incorrect'], 404);
        }

        // Envoie notif
        $this->notificationService->createNotification($sender, $receiver, 'like', 3);
        return $this->json(['message' => 'Ton systeme de notif fonctionne gros BG']);
    }
}