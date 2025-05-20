<?php

namespace App\Controller;

use App\Entity\UserWords;
use App\Entity\Words;
use App\Form\WordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WordSentController extends AbstractController
{
    #[Route('/word/sent', name: 'app_word_sent')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $formWords = $this->createForm(WordType::class);
        $formWords->handleRequest($request);

        if ($formWords->isSubmitted() && $formWords->isValid()) {
            $currentUser = $this->getUser();

            // On récupère le tableau des 3 mots du formulaire
            $wordsTexts = $formWords->get('word')->getData();

            foreach($wordsTexts as $text) {
                $word = new Words();
                $word->setWord($text);
                $entityManager->persist($word);

                $userWords = new UserWords();
                $userWords->setSender($currentUser);
                $userWords->setWords($word); 
                $userWords->setStatus('pending');
                $entityManager->persist($userWords);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_feed');
        }

        return $this->render('feed/create_words.html.twig', [
            'formWords' => $formWords->createView(),
        ]);
    }
}
