<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Haikus;
use App\Form\CommentType;
use App\Repository\EntityTypeRepository;
use App\Repository\NotificationsRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{

    #[Route('/feed/{id}/comment', name: 'app_comment_add', methods: ['POST'])]
        public function addComment(Request $request, Haikus $haiku, EntityManagerInterface $entityManager, NotificationService $notificationService): Response 
        {
            $user= $this->getUser();

            $comment = new Comments();
            $comment->setHaiku($haiku);
            $comment->setSender($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());

            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($comment);

                // Création de la notification
                $notificationService->createNotification(
                    $user,
                    $haiku->getCreator(),
                    'comment',
                    $haiku->getId()
                );
                $entityManager->flush();

                return $this->redirectToRoute('app_feed');
            }

            return new Response ('Formulaire invalide', 400);
        }

    #[Route('/suppr/comment/{id}', name: 'delete_comment', methods: ['POST'])]
    public function deleteComment(Comments $comment, Request $request, EntityManagerInterface $entityManager, NotificationsRepository $notificationRepo, EntityTypeRepository $entityTypeRepo): Response
    {
        $user = $this->getUser();
        $haiku = $comment->getHaiku(); 
        $entityType = $entityTypeRepo->findOneBy(['name' => 'comment']);

        $notification = $notificationRepo->findOneBy([
            'Sender' => $user,
            'Receiver' => $haiku->getCreator(),
            'entity_type' => $entityType,
            'entity_id' => $haiku->getId(),
        ]);
        
        // On vérifie que le user est bien celui qui fait le commentaire
        if($comment->getSender() !== $user) {
            throw $this->createAccessDeniedException("Vous n'avez pas la permission de supprimer ce commentaire");
        }

        // On vérifie si le token est valide et on suppr
        if($this->isCsrfTokenValid('SUP' . $comment->getId(),$request->get('_token'))) {
            if($notification) {
                $entityManager->remove($notification);
            }
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire supprimé');
        } else {
            $this->addFlash('error', 'Token invalide');
        }

        // On récupère l'URL d'où vient la requete pour y retourner. Si on trouve pas alors on va sur le feed
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_feed'));
    }
}
