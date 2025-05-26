<?php

namespace App\Service;

use App\Entity\Comments;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\HaikusRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HaikuViewService {

    private HaikusRepository $haikusRepository;
    private LikeService $likeService;
    private FormFactoryInterface $formFactory;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(HaikusRepository $haikusRepository, LikeService $likeService, FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator) {

        $this->haikusRepository = $haikusRepository;
        $this->likeService = $likeService;

        // On doit injecter manuellement dans un service car on a pas l'héritage de abstractController comme dans les controleurs
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

public function getHaikusFor(string $mode, ?User $currentUser = null): array
    {
        switch ($mode) {
            // Pour la page profil utilisateur
            case 'user':
                $haikus = $this->haikusRepository->findBy(['creator' => $currentUser]);
                break;

                // Pour le feed général
            case 'feed':
                $haikus = $this->haikusRepository->findAllExceptByUser($currentUser);
                break;
            
            // Pour une catégorie les plus likes
            case 'most_liked':
                $haikus = $this->haikusRepository->findMostLiked();
                break;

            // Pour une catégorie sélection de la semaine 
            case 'week_selection':
                $haikus = $this->haikusRepository->findWeekSelection();
                break;
            
            default:
                $haikus = $this->haikusRepository->findAll();
                break;
        }

         // Initialisation tableau vide like
        $likedHaikus = [];
        // Tableau pour stocker un formulaire par haiku
        $commentForms = [];
        

        if($currentUser) {
            foreach($haikus as $haiku) {
                // Si le haiku est liké par l'utilisateur, on ajoute son ID au tableau
                if ($this->likeService->isHaikuLikedByUser($haiku, $currentUser)) {
                    $likedHaikus[] = $haiku->getId(); // Ajout de l'ID du haiku
                }

                // Création d'un formulaire spécifique pour chaque haiku intégré dans la boucle
                $comment = new Comments();
                $comment->setHaiku($haiku);
                $comment->setSender($currentUser);

                $form = $this->formFactory->create(CommentType::class, $comment, [
                    'action' => $this->urlGenerator->generate('app_comment_add', ['id' => $haiku->getId()]), // On envoie les données de la route + l'id du haiku concerné
                    'method' => 'POST',
                ]);

                $commentForms[$haiku->getId()] = $form->createView();
            }
        }

        return [
            'haikus' => $haikus,
            'likedHaikus' => $likedHaikus,
            'commentForms' => $commentForms,
        ];
    }
}