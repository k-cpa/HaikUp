<?php

namespace App\Controller;

use App\Entity\Haikus;
use App\Form\WriteHaikuType;
use App\Repository\EntityTypeRepository;
use App\Repository\NotificationsRepository;
use App\Repository\UserWordsRepository;
use App\Service\ChatGptService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class HaikuController extends AbstractController
{
    #[Route('/haiku/new', name: 'app_create_haiku')]
    public function write(Request $request, EntityManagerInterface $entityManager, UserWordsRepository $userWords, NotificationService $notificationService): Response
    {
        // On récupère le user pour le passer en paramètre et vérifier que le User ne peut pas créer un Haiku avec ses propres mots. 
        $user = $this->getUser();
        $packs = $userWords->findPendingPacksForUser($user);

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

        // on initialise un haiku vide
        $haiku = new Haikus();

        $formHaiku = $this->createForm(WriteHaikuType::class, $haiku);
        $formHaiku->handleRequest($request);

        if ($formHaiku->isSubmitted() && $formHaiku->isValid()) {
            
            $haiku->setCreator($user);
            $entityManager->persist($haiku);

            // On initialise un tableau vide pour les mots
            $words = [];

            foreach ($userWordsList as $userWord) {

                $words[] = $userWord->getWords()->getWord();
                $userWord->setReceiver($user);
                $userWord->setStatus('used');
                $userWord->setHaiku($haiku);
                $entityManager->persist($userWord);
            }

            // Un premier flush obligatoire pour récupérer l'id du haiku -> optimisation possible ?
            $entityManager->flush();


            // Création de la notification
            $notificationService->createNotification(
                $user,
                $userWordsList[0]->getSender(),
                'haiku',
                $haiku->getId(),
            );

            $entityManager->flush();

            return $this->redirectToRoute('app_feed');
        }


        return $this->render('user_pages/create_haiku.html.twig', [
            'formHaiku' => $formHaiku->createView(),
            'userWordsList' => $userWordsList,
        ]);
    }

    #[Route('/haiku/no-words', name: 'app_no_words')]
    public function index(Request $request): Response
    {
// dd($_ENV['OPENAI_API_KEY'] ?? null, $_SERVER['OPENAI_API_KEY'] ?? null, getenv('OPENAI_API_KEY'));
        $referer = $request->headers->get('referer');
        if ($referer) {
            $request->getSession()->set('last_origin_url', $referer);
        }

        return $this->render('user_pages/no_words.html.twig');
    }

    #[Route('/haiku/ai-generation', name: 'app_haiku_generate_ia')]
    public function haikuWithGpt(Request $request, ChatGptService $chatGptService, EntityManagerInterface $entityManager,): Response
    {
        $user = $this->getUser();
    //    On setup pour récupérer l'URL d'avant connexion à la page no words dans laquelle il a demandé un thème à GPT -> cf route no words juste au dessus
        $session = $request->getSession();
        $originUrl = $session->get('last_origin_url');
        $theme = $request->request->get('theme', '');
        $generatedWords = $chatGptService->generateWords($theme);

        $haiku = new Haikus();
        $formHaiku = $this->createForm(WriteHaikuType::class, $haiku);
        $formHaiku->handleRequest($request);
        
        if ($formHaiku->isSubmitted() && $formHaiku->isValid()) {
            $haiku->setCreator($user);
            $entityManager->persist($haiku);
            $entityManager->flush();

            if ($originUrl) {
                return $this->redirect($originUrl);
            } 
            return $this->redirectToRoute('app_feed');
        }
        return $this->render('user_pages/create_haiku.html.twig', [
            'formHaiku' => $formHaiku,
            'generatedWords' => $generatedWords,
            'usingAIwords' => true,
            'theme' => $theme,
        ]);
    }


    

    #[Route('/haiku/{id}/delete', name: 'delete_haiku', methods: ['POST'])]
    public function deleteHaiku(Haikus $haiku, Request $request, EntityManagerInterface $entityManager, NotificationsRepository $notificationRepo, EntityTypeRepository $entityTypeRepo): Response
    {
        $user = $this->getUser();

        $allNotifications = $notificationRepo->findBy([
            'entity_id' => $haiku->getId(),
        ]);

        // On vérifie que le user est bien celui qui fait le haiku
        if ($haiku->getCreator() !== $user) {
            throw $this->createAccessDeniedException("Vous n'avez pas la permission de supprimer ce post");
        }
        // On vérifie si le token est valide et on suppr
        if($this->isCsrfTokenValid('SUP' . $haiku->getId(), $request->get('_token'))) {
            // userWords = delete on cascade mais pour supprimer les mots on va le faire manuellement ici. Si on supprime le haiku on boucle sur chaque mot pour remove
            foreach ($haiku->getUserWords() as $userWords) {
                $userWords->setHaiku(NULL);
                $userWords->setStatus('pending');

                $entityManager->persist($userWords);
            }

            foreach ($allNotifications as $notif) {
                $entityManager->remove($notif);
            }

            $entityManager->remove($haiku);
            $entityManager->flush();
            $this->addFlash('success', 'Haiku supprimé');
        } else {
            $this->addFlash('error', 'Token invalide');
        }
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_feed'));
    }

    #[Route('/haiku/{id}/report', name: 'report_haiku')]
    public function reportHaiku(Haikus $haiku, MailerInterface $mailer): Response
    {

        $reportingUser = $this->getUser();
        $haikuAuthor = $haiku->getCreator();

         $emailText = "Signalement automatique de haïku\n\n";

        // Infos utilisateur qui signale
        $emailText .= "Email du profil à l'origine du signalement : " .  $reportingUser->getUserIdentifier() . "\n";

        // Infos haiku et son auteur
        $emailText .= "ID du haïku signalé : " . $haiku->getId() . "\n";
        $emailText .= "Contenu du haïku:\n";
        $emailText .= $haiku->getLine1() . "\n";
        $emailText .= $haiku->getLine2() . "\n";
        $emailText .= $haiku->getLine3() . "\n\n";

        $emailText .= "Informations sur l'auteur du haïku :\n";
        $emailText .= "Nom : " . $haikuAuthor->getUsername(). "\n";
        $emailText .= "Email : " . $haikuAuthor->getEmail() . "\n";

        $email = (new Email())
            ->from('contact@kevcampana.com')
            ->to('kevcampana@gmail.com')
            ->subject('Signalement automatique de haïku')
            ->text($emailText);

        $mailer->send($email);

        $this->addFlash('success', 'Le haïku a été signalé. Merci pour votre retour.');

        return $this->redirectToRoute('app_feed');
    }
}
