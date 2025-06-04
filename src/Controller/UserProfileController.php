<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AddHaikuToCollectionType;
use App\Form\AddProfilDescriptionType;
use App\Repository\CollectionsRepository;
use App\Repository\FollowsRepository;
use App\Repository\HaikusRepository;
use App\Service\HaikuViewService;
use App\Service\SubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserProfileController extends AbstractController
{

    private CollectionsRepository $collections;
    private EntityManagerInterface $entityManager;

    public function __construct(CollectionsRepository $collections, EntityManagerInterface $entityManager)
    {
        $this->collections = $collections;
        $this->entityManager = $entityManager;
    }

    // Page accueil du profil utilisateur avec preview de ses haikus et des haikus likés
     #[Route('/mon-profil', name: 'app_user_profile')]  
    public function userPage(FollowsRepository $followRepository, HaikusRepository $haikusRepository): Response
    {
        $user = $this->getUser();

        $followersCount = $followRepository->countFollowers($user);
        $followsCount = $followRepository->countFollows($user);
        $totalHaikus = $haikusRepository->numberOfUserHaiku($user);

        // Pour afficher un preview des haikus de l'utilisateur
        $haikus = $haikusRepository->findBy(
            ['creator' => $user],
            null, // Pas de tri (confirmer ?)
            3, // Limite à 3 haikus pour preview
        );

        $haikuLikedByUser = $haikusRepository->findAllHaikusLikedByUser($user);

        return $this->render('user_pages/profile.html.twig', [
            'haikus' => $haikus,
            'haikuLikedByUser' => $haikuLikedByUser,
            'followersCount' => $followersCount,
            'followsCount' => $followsCount,
            'totalHaikus' => $totalHaikus
        ]);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////



    // Page d'ajout de la description au profil
    #[Route('/mon-profil/description', name: 'app_add_description')]
    public function addProfilDescription(Request $request): Response
    {
        $user = $this->getUser();

        // GESTION DU FORMULAIRE D'AJOUT DE LA DESCRIPTION DU PROFIL
        $formAddDescription = $this->createForm(AddProfilDescriptionType::class, $user);
        $formAddDescription->handleRequest($request);

        if ($formAddDescription->isSubmitted() && $formAddDescription->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_user_profile');

        }

        return $this->render('user_pages/add_description.html.twig', [
            'formAddDescription' => $formAddDescription,
        ]);
    }


    //////////////////////////////////////////////////////////////////////////////////////////////////////



    // Page où l'utilisateur peut voir tous ses haikus et les ranger dans des collections
    #[Route('/profile/haikus', name: 'app_user_haikus')]
    public function userHaikus(HaikuViewService $haikuViewService): Response
    {
        $user = $this->getUser();
        $data = $haikuViewService->getHaikusFor('user', $user);
        $collections = $this->collections->findOneByUser($user);

        

        if($user) {

            $addHaikuToCollectionForms = [];
            $haikus = $data['haikus'];
            // dump($haikus); die;

            foreach($haikus as $haiku) {
                $form = $this->createForm(AddHaikuToCollectionType::class, null, [
                    'action' => $this->generateUrl('collection_add_haiku', ['HaikuId' => $haiku->getId()]),
                    'method' => 'POST',
                ]);

                $addHaikuToCollectionForms[$haiku->getId()] = $form->createView();
            }

        }
        // J'utilise un tableau d'objet récupérés via le service donc on récupère un tableau d'ID 
        // Chaque commentForm est indexé par l'ID du haïku
        // addCollection -> comme commentForm on indexe via l'ID du haïku

        return $this->render('user_pages/profile.html.twig', [
            'haikus' => $data['haikus'],
            'likedHaikus'=>$data['likedHaikus'],
            'commentForms' => $data['commentForms'],
            'collections' => $collections,
            'addCollectionToHaikuForms' => $addHaikuToCollectionForms
        ]);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////

    
    // Page de profile des autres utilisateurs
    #[Route('/profil/{id}', name: 'app_user_show')]
    public function otherUserPages(?User $user, FollowsRepository $followRepository, HaikusRepository $haikusRepository): Response
    {
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Pour afficher un preview des haikus de l'utilisateur
        $haikus = $haikusRepository->findBy(
            ['creator' => $user],
            null, // Pas de tri (confirmer ?)
            3, // Limite à 3 haikus pour preview
        );
        $totalHaikus = $haikusRepository->numberOfUserHaiku($user);

        $followersCount = $followRepository->countFollowers($user);
        $followsCount = $followRepository->countFollows($user);

        // Gestion du bouton d'abonnement
        $viewer = $this->getUser();
        $isSubscribed = false;

        if ($viewer && $viewer !== $user) {
            $isSubscribed = $followRepository->findOneBy([
                'Sender' => $viewer,
                'Receiver' => $user,
            ]) !== null;
        }

        return $this->render('user_pages/other_profile.html.twig', [
            // Unpacking de tableau associatif -> Ca permet de décomposer le tableau et fusionner les paires clé/valeur direct 
            'user' => $user,
            'haikus' => $haikus,
            'totalHaikus' => $totalHaikus,
            'followersCount' => $followersCount,
            'followsCount' => $followsCount,
            'isSubscribed' => $isSubscribed,
        ]);
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////

    #[Route('/profil/{id}/subscription', name: 'user_follow', methods: ['POST'])] 
    public function followUser(Request $request, User $user, SubscriptionService $subscriptionService, FollowsRepository $followRepository): Response
    {
        $sender = $this->getUser();

        if (!$sender) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Utilisateur non connecté'
            ], 403);
        }

        // Protection de route si pas AJAX -> peut être overkill avec CSRF + POST mais tant pis
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => false, 'message' => 'Requête non autorisée'], 400);
        }
         // Vérifie si le token est valide
        if (!$this->isCsrfTokenValid('ajax', $request->headers->get('X-CSRF-Token'))) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
        }

        try {
            $subscribed = $subscriptionService->toggleSubscription($sender, $user);
            return new JsonResponse([
                'success' => true,
                'subscribed' => $subscribed, 
                'followersCount' => $followRepository->count(['Receiver' => $user]),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false, 
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // temporairement
            ]);
        }
    }
}
