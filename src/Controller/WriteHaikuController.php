<?php

namespace App\Controller;

use App\Entity\Haikus;
use App\Entity\User;
use App\Entity\UserWords;
use App\Form\WriteHaikuType;
use App\Repository\UserWordsRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WriteHaikuController extends AbstractController
{
    #[Route('/create/haiku', name: 'app_create_haiku')]
    public function write(Request $request, EntityManagerInterface $entityManager, UserWordsRepository $userWords, NotificationService $notificationService): Response
    {
        $packs = $userWords->findPendingPacksForUser();

        // On reprend un pack random sinon on met null
        $pack = $packs[array_rand($packs)] ?? null;

        if ($pack) {
            $userWordsList = $userWords->findBy([
                'sender' => $pack['sender_id'],
                'created_at' => $pack['created_at'],
                'status' => 'pending',
                'Receiver' => null,
            ]);
        } else {
            $userWordsList = [];
        }

        $currentUser = $this->getUser();

        // on initialise un haiku vide
        $haiku = new Haikus();

        $formHaiku = $this->createForm(WriteHaikuType::class, $haiku);
        $formHaiku->handleRequest($request);

        if ($formHaiku->isSubmitted() && $formHaiku->isValid()) {
            
            $haikuContent = $formHaiku->getData();
            
            $haiku->setContent($haikuContent);
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
}
