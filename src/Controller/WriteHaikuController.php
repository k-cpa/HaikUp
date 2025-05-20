<?php

namespace App\Controller;

use App\Entity\Haikus;
use App\Form\WriteHaikuType;
use App\Repository\UserWordsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WriteHaikuController extends AbstractController
{
    #[Route('/write/haiku', name: 'app_write_haiku')]
    public function write(Request $request, EntityManagerInterface $entityManager, UserWordsRepository $userWords): Response
    {
        $currentUser = $this->getUser();
        
        $userWords->findOneBy([
            'receiver' => $currentUser,
            'status' => 'pending',
        ]);

        $haiku = new Haikus();

        $form = $this->createForm(WriteHaikuType::class, $haiku);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $haikuContent = $form->getData();
            
            $haiku->setContent($haikuContent);
            $haiku->setCreator($currentUser);
            // $haiku->setUserWords() voir après comment intégrer ça 
            $entityManager->persist($haiku);
        }


        return $this->render('write_haiku/index.html.twig', [
            'controller_name' => 'WriteHaikuController',
        ]);
    }
}
