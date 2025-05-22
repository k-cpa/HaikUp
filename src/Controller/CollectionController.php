<?php

namespace App\Controller;

use App\Entity\Collections;
use App\Form\AddHaikuToCollectionType;
use App\Form\CollectionType;
use App\Repository\HaikusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CollectionController extends AbstractController
{


    #[Route('/collection', name: 'app_collection_add')]
    public function manageCollection(): Response
    {
        $user = $this->getUser();


        return $this->render('user_pages/collection.html.twig', [
            
        ]);
    }

    #[Route('/collection/new', name: 'app_collection_add')]
    public function createCollection(Request $request, EntityManagerInterface $entityManager): Response
    {   
        $collectionForm = $this->createForm(CollectionType::class);
        $collectionForm->handleRequest($request);


        if ($collectionForm->isSubmitted() && $collectionForm->isValid()) {
            $user = $this->getUser();
            $collectionName = $collectionForm->get('name')->getData();

            $collection = new Collections();
            $collection->setCreator($user);
            $collection->setName($collectionName);
            $collection->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($collection);
            $entityManager->flush();

            return $this->redirectToRoute(('app_user_profile'));


        }
        return $this->render('user_pages/add_collection.html.twig', [
            'collectionForm' => $collectionForm,
        ]);
    }

    #[Route('/collection/{HaikuId}/add_collection', name:'collection_add_haiku', methods: ['POST'])]
    public function addCollectionToHaiku(int $haikuId, Request $request, EntityManagerInterface $entityManager, HaikusRepository $haikusRepository): Response
    {

        $user = $this->getUser();
        $haiku = $haikusRepository->find($haikuId);

        if(!$haiku) {
            throw $this->createNotFoundException('Haïku introuvable.');
        }
        if ($haiku->getCreator() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cet Haiku');
        }

        $addHaikuToCollectionForm = $this->createForm(AddHaikuToCollectionType::class);
        $addHaikuToCollectionForm->handleRequest($request);

        if ($addHaikuToCollectionForm->isSubmitted() && $addHaikuToCollectionForm->isValid()) {
            $haikuCollection = $addHaikuToCollectionForm->getData();

            $haiku->setCollection($haikuCollection);
            $entityManager->persist($haiku);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_profile');
    }
}
