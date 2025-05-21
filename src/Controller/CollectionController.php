<?php

namespace App\Controller;

use App\Entity\Collections;
use App\Form\CollectionType;
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

            $entityManager->persist($collection);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile');

        }
        return $this->render('user_pages/add_collection.html.twig', [
            'collectionForm' => $collectionForm,
        ]);
    }
}
