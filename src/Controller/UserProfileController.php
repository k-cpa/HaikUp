<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AddHaikuToCollectionType;
use App\Repository\CollectionsRepository;
use App\Repository\HaikusRepository;
use App\Service\HaikuViewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserProfileController extends AbstractController
{

    private CollectionsRepository $collections;

    public function __construct(CollectionsRepository $collections)
    {
        $this->collections = $collections;
    }

    // Page de profil de l'utilisateur connecté
    #[Route('/profile', name: 'app_user_profile')]
    public function userPage(HaikuViewService $haikuViewService): Response
    {
        $user = $this->getUser();
        $data = $haikuViewService->getHaikusFor('user', $user);
        $collections = $this->collections->findOneByUser($user);

        if($user) {
            foreach($data as $haiku) {
                $form = $this->createForm(AddHaikuToCollectionType::class, null, [
                    'action' => $this->generateUrl('collection_add_haiku', ['haikuId' => $haiku->getId()]),
                    'method' => 'POST',
                ]);

                $addHaikuToCollectionForms[$haiku->getId()] = $form->createView();
            }

        }
        

        return $this->render('user_pages/profile.html.twig', [
            'haikus' => $data,
            'collections' => $collections,
            'addCollectionToHaikuForms' => $addHaikuToCollectionForms,
        ]);
    }



    // Page de profile des autres utilisateurs
    #[Route('/users/{id}', name: 'app_user_show')]
    public function otherUserPages(User $user, HaikuViewService $haikuViewService): Response
    {
        $data = $haikuViewService->getHaikusFor('user', $user);
        $collections = $this->collections->findOneByUser($user);

        return $this->render('user_pages/other_profile.html.twig', [
            // Unpacking de tableau associatif -> Ca permet de décomposer le tableau et fusionner les paires clé/valeur direct 
            ...$data,
            'collections' => $collections,
        ]);
    }
}
