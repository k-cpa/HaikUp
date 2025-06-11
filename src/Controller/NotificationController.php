<?php 

namespace App\Controller;

use App\Entity\Haikus;
use App\Entity\Notifications;
use App\Entity\User;
use App\Repository\FollowsRepository;
use App\Repository\HaikusRepository;
use App\Repository\NotificationsRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{

    private $notificationService;
    private $notificationsRepository;
    private $entityManager;

    // Injection du service custom 
    public function __construct(NotificationService $notificationService, EntityManagerInterface $entityManager, NotificationsRepository $notificationsRepository) 
    {
        $this->notificationService = $notificationService;
        $this->notificationsRepository = $notificationsRepository;
        $this->entityManager = $entityManager; // On injecte ici l'EntityManager
    }

    // Redirection au click sur la modale de notification turbo stream
    #[Route('/notifications/{id}/go', name: 'notification_go')]
    public function goToTarget(int $id, Security $security, HaikusRepository $haiku, FollowsRepository $follow): Response
    {
        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $notification = $this->entityManager->getRepository(\App\Entity\Notifications::class)->find($id);

        if (!$notification || $notification->getReceiver() !== $user) {
            throw $this->createNotFoundException('Notification introuvable ou accès refusé');
        }

        $notification->setStatus(true);
        $this->entityManager->flush();

        // Rediriger vers l'entité cible
        $entityType = $notification->getEntityType()->getName();
        $entityId = $notification->getEntityId();

        switch ($entityType) {

            // like et commentaire vont rediriger vers la page du haiku 
            case 'haiku':
            case 'comment':
            case 'like':
                $haikuEntity = $haiku->find($entityId);

                if(!$haikuEntity) {
                    throw $this->createNotFoundException('Haïku introuvable');
                };

                $haikuId = $haikuEntity->getId();
                $userId = $haikuEntity->getCreator()->getId();

                $url = $this->generateUrl('app_user_haikus', ['userId' => $userId]) . '#haiku-' . $haikuId;
                return $this->redirect($url);

            // Follow va rediriger vers la page de l'utilisateur où il peut accéder à sa liste de followers. 
            // On récupère d'abord l'entité follows associée à l'ID
            case 'follow':
                $followEntity = $follow->find($entityId);

                if(!$followEntity) {
                    throw $this->createNotFoundException('Notification introuvable');
                };

                $followId = $followEntity->getSender()->getId();


                return $this->redirectToRoute('app_user_show', ['id' => $followId,]);

            

            default:
                return $this->redirectToRoute('homepage');
        }

        // Par défaut, redirige vers la page d'accueil
        return $this->redirectToRoute('homepage');
    }


    #[Route('/notifications/unread', name:'app_notifications_unread')] 
    public function unreadNotifications(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json([], 401);
        }

        $notifications = $this->notificationsRepository->findBy(
            ['Receiver' => $user, 'status' => false], 
            ['created_at' => 'DESC']
        );

        $data = [];
        foreach ($notifications as $notification) {
            $sender = $notification->getSender();

            $data[] = [
                'id' => $notification->getID(),
                'sender' => $sender->getUsername(),
                'message' => $notification->getMessage(),
                'createdAt' => $notification->getCreatedAt(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/profil/notifications', name:'app_notifications')] 
    public function notificationsPages(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $notifications = $this->notificationsRepository->findBy(
            ['Receiver' => $user],
            ['created_at' => 'DESC'],
        );

        return $this->render('user_pages/notifications.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}