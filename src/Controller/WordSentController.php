<?php

namespace App\Controller;

use App\Entity\UserWords;
use App\Entity\Words;
use App\Form\WordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WordSentController extends AbstractController
{
    #[Route('/word/sent', name: 'app_word_sent')]
    public function index(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {

        $form = $this->createForm(WordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $security->getuser();

            // On récupère le tableau des 3 mots du formulaire
            $wordsTexts = $form->get('words')->getData();

            foreach($wordsTexts as $text) {
                $word = new Words();
                $word->setWord($text);
                $entityManager->persist($word);

                $userWords = new UserWords();
                $userWords->set
// Reprendre ici 
            }
        }
        return $this->render('word_sent/index.html.twig', [
            'controller_name' => 'WordSentController',
        ]);
    }
}
