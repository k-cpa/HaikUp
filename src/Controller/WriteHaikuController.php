<?php

namespace App\Controller;

use App\Entity\Haikus;
use App\Entity\User;
use App\Form\WriteHaikuType;
use App\Repository\UserWordsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WriteHaikuController extends AbstractController
{
    #[Route('/create/haiku', name: 'app_create_haiku')]
    public function write(Request $request, EntityManagerInterface $entityManager, UserWordsRepository $userWords): Response
    {
        $packs = $userWords->findPendingPacksForUser();

        // On reprend le premier pack trouvé sinon on met null
        $pack = $packs[0] ?? null;

        $senderUser = $entityManager->getRepository(User::class)->find($pack['sender_id']);

        if ($pack) {
            $userWordsList = $userWords->findBy([
                'sender' => $senderUser,
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
            // $haiku->setUserWords() voir après comment intégrer ça 
            $entityManager->persist($haiku);
            $words = [];


            foreach ($userWordsList as $userWord) {

                $words[] = $userWord->getWords()->getWord();
                $userWord->setReceiver($currentUser);
                $userWord->setStatus('used');
                $userWord->setHaiku($haiku);
                $entityManager->persist($userWord);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_feed');
        }


        return $this->render('feed/create_haiku.html.twig', [
            'formHaiku' => $formHaiku->createView(),
            'userWordsList' => $userWordsList,
        ]);
    }
}
