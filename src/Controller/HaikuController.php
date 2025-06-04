<?php

namespace App\Controller;

use App\Entity\Haikus;
use App\Form\WriteHaikuType;
use App\Repository\EntityTypeRepository;
use App\Repository\NotificationsRepository;
use App\Repository\UserWordsRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HaikuController extends AbstractController
{
    #[Route('/haiku/new', name: 'app_create_haiku')]
    public function write(Request $request, EntityManagerInterface $entityManager, UserWordsRepository $userWords, NotificationService $notificationService): Response
    {
        $packs = $userWords->findPendingPacksForUser();
        // DIFFERENT USER MOTHERFUCKER

        if (!empty($packs)) {
            $pack = $packs[array_rand($packs)];

            $userWordsList = $userWords->findBy([
                'sender' => $pack['sender_id'],
                'created_at' => $pack['created_at'],
                'status' => 'pending',
                'Receiver' => null,
            ]);
        } else {
            return $this->redirectToRoute('app_no_words');
        }

        $currentUser = $this->getUser();

        // on initialise un haiku vide
        $haiku = new Haikus();

        $formHaiku = $this->createForm(WriteHaikuType::class, $haiku);
        $formHaiku->handleRequest($request);

        if ($formHaiku->isSubmitted() && $formHaiku->isValid()) {
            
            $haiku->setCreator($currentUser);
            $entityManager->persist($haiku);

            // On initialise un tableau vide pour les mots
            $words = [];

            foreach ($userWordsList as $userWord) {

                $words[] = $userWord->getWords()->getWord();
                $userWord->setReceiver($currentUser);
                $userWord->setStatus('used');
                $userWord->setHaiku($haiku);
                $entityManager->persist($userWord);
            }

            // Un premier flush obligatoire pour récupérer l'id du haiku -> optimisation possible ?
            $entityManager->flush();


            // Création de la notification
            $notificationService->createNotification(
                $currentUser,
                $userWordsList[0]->getSender(),
                'haiku',
                $haiku->getId(),
            );

            $entityManager->flush();

            return $this->redirectToRoute('app_feed');
        }


        return $this->render('feed/create_haiku.html.twig', [
            'formHaiku' => $formHaiku->createView(),
            'userWordsList' => $userWordsList,
        ]);
    }

    #[Route('/haiku/no-words', name: 'app_no_words')]
    public function index()
    {
        return $this->render('feed/no_words.html.twig');
    }
    

    #[Route('/haiku/{id}/delete', name: 'delete_haiku', methods: ['POST'])]
    public function deleteHaiku(Haikus $haiku, Request $request, EntityManagerInterface $entityManager, NotificationsRepository $notificationRepo, EntityTypeRepository $entityTypeRepo): Response
    {
        $user = $this->getUser();
        $entityType = $entityTypeRepo->findOneBy(['name' => 'haiku']);
        $notification = $notificationRepo->findOneBy([
            'Sender' => $user,
            'Receiver' => $haiku->getCreator(),
            'entity_type' => $entityType,
            'entity_id' => $haiku->getId(),
        ]);
        // On vérifie que le user est bien celui qui fait le haiku
        if ($haiku->getCreator() !== $user) {
            throw $this->createAccessDeniedException("Vous n'avez pas la permission de supprimer ce post");
        }
        // On vérifie si le token est valide et on suppr
        if($this->isCsrfTokenValid('SUP' . $haiku->getId(), $request->get('_token'))) {

            // userWords = delete on cascade mais pour supprimer les mots on va le faire manuellement ici. Si on supprime le haiku on boucle sur chaque mot pour remove
            foreach ($haiku->getUserWords() as $userWord) {
                $word = $userWord->getWords();

                $entityManager->remove($word);
            }

            if($notification) {
                $entityManager->remove($notification);
            }
            $entityManager->remove($haiku);
            $entityManager->flush();
            $this->addFlash('success', 'Haiku supprimé');
        } else {
            $this->addFlash('error', 'Token invalide');
        }
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_feed'));
    }
}
