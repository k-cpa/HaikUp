<?php

namespace App\Controller;

use App\Entity\Comments;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{

    #[Route('/suppr/comment/{id}', name: 'delete_comment', methods: ['POST'])]
    public function deleteComment(Comments $comment, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // On vérifie que le user est bien celui qui fait le commentaire
        if($comment->getSender() !== $user) {
            throw $this->createAccessDeniedException("Vous n'avez pas la permission de supprimer ce commentaire");
        }

        // On vérifie si le token est valide et on suppr
        if($this->isCsrfTokenValid('SUP' . $comment->getId(),$request->get('_token'))) {
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
