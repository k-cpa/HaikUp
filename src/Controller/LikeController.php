<?php 

namespace App\Controller;

use App\Entity\EntityType;
use App\Entity\Haikus;
use App\Entity\Likes;
use App\Entity\Notifications;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('/haiku/{id}/like', name:'haiku_like', methods: ['POST'])]
    public function likeHaiku (int $id, EntityManagerInterface $entityManager, NotificationService $notificationService, Security $security): Response {

        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $haiku = $entityManager->getRepository(Haikus::class)->find($id);
        if (!$haiku) {
            throw $this->createNotFoundException('Haiku non trouvé');
        }

        $existingLike = $entityManager->getRepository(Likes::class)->findOneBy([
            'user' => $user,
            'haiku' => $haiku,
        ]);
        if ($existingLike) {
            $entityManager->remove($existingLike);

            $notification = $entityManager->getRepository(Notifications::class)->findOneBy([
                'sender' => $user,
                'receiver' => $haiku->getCreator(),
                'entityType' => $entityManager->getRepository(EntityType::class)->findOneBy(['name' => 'like']),
                'entityId' => $haiku->getId(),
            ]);

            if($notification) {
                $entityManager->remove($notification);
            }

            $entityManager->flush();

            return $this->redirectToRoute('haiku_show', ['id' => $id]);
        }
    }
}