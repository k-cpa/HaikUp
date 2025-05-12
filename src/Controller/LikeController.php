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

        // Vérif si le user a déjà like 
        $existingLike = $entityManager->getRepository(Likes::class)->findOneBy([
            'user' => $user,
            'haiku' => $haiku,
        ]);
        if ($existingLike) {
            $entityManager->remove($existingLike);

            // Recherche d'une notification liée au like -> Si on supprime un like on supprime sa notif
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

        // Si pas de like alors on va le créer 
        $like = new Likes();
        $like->setSender($user);
        $like->setHaiku($haiku);

        $entityManager->persist($like);

        // Création de la notif -> appelle du service custom [/src/Service/]
        $notificationService->createNotification(
            $user, // le sender
            $haiku->getCreator(), // le receiver = personne qui a fait le haiku
            'like', // name de l'entityType
            $haiku->getId() // l'id du haiku concerné
        );
        
        $entityManager->flush();

        return $this->redirectToRoute('haiku_show', ['id' => $id]);
    }
}