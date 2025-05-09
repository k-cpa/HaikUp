<?php 

namespace App\Controller;

use App\Entity\User;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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

    // Redirection au click sur la modale de notification turbo stream
    #[Route('/notifications/{id}/go', name: 'notification_go')]
    public function goToTarget(int $id, Security $security): Response
    {
        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $notification = $this->entityManager->getRepository(\App\Entity\Notifications::class)->find($id);

        if (!$notification || $notification->getReceiver() !== $user) {
            throw $this->createNotFoundException('Notification introuvable ou accès refusé');
        }

        // Marquer les notifications en "lu" + redirection vers la page avec gestion Turbo Stream 
        $notification->setStatus(true);
        $this->entityManager->flush();

        // Rediriger vers l'entité cible
        $entityType = $notification->getEntityType()->getName();
        $entityId = $notification->getEntityId();

        switch ($entityType) {

            // like et commentaire vont rediriger vers la page du haiku 
            case 'haiku':
            case 'comment':
                return $this->redirectToRoute('haiku_show', ['id' => $entityId]);

            // Follow va rediriger vers la page de l'utilisateur où il peut accéder à sa liste de followers. 
            case 'follow':
                return $this->redirectToRoute('user_profile', ['id' => $entityId]);

            default:
                return $this->redirectToRoute('homepage');
        }

        // Par défaut, redirige vers la page d'accueil
        return $this->redirectToRoute('homepage');
    }

    // Logique de test
    // #[Route('/test-notification/{receiverId}', name: 'test_notification')]
    // public function testNotification(int $receiverId, Security $security): JsonResponse {

    //     // Exemple de création notif 
    //     $sender = $security->getUser();
    //     if(!$sender) {
    //         return $this->json(['error' => 'Utilisateur non authent', 401]);
    //     }
    //     $receiver = $this->entityManager->getRepository(User::class)->find($receiverId);

    //     if(!$receiver) {
    //         return $this->json(['error' => 'User incorrect'], 404);
    //     }

    //     // Envoie notif
    //     $this->notificationService->createNotification($sender, $receiver, 'like', 3);
    //     return $this->json(['message' => 'Ton systeme de notif fonctionne gros BG']);
    // }
}