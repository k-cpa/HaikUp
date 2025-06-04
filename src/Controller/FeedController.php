<?php

namespace App\Controller;

use App\Repository\HaikusRepository;
use App\Service\HaikuViewService;
use App\Service\LikeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FeedController extends AbstractController
{
    private $haikusRepository;
    private $likeService;

    public function __construct(LikeService $likeService, HaikusRepository $haikusRepository) {
        $this->likeService = $likeService;
        $this->haikusRepository = $haikusRepository;
    }

    #[Route('/feed', name: 'app_feed')]
    public function index(HaikuViewService $haikuViewService): Response
    {
        
        $user = $this->getUser();

        // Tableau associatif que la méthode retourne
        $data = $haikuViewService->getHaikusFor('feed', $user); 

       

        return $this->render('feed/feed.html.twig', $data);
    }

    #[Route('/feed/{id}/like', name: 'feed_haiku_like', methods: ['POST'])] 
        public function likeHaiku(int $id, Request $request): Response
        {

            // Protection de route si pas AJAX -> peut être overkill avec CSRF + POST mais tant pis
            if (!$request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Requête non autorisée'], 400);
            } 
            
            // Vérifie si le token est valide
            if (!$this->isCsrfTokenValid('ajax', $request->headers->get('X-CSRF-Token'))) {
                return new JsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            }

            $haiku = $this->haikusRepository->find($id);
            if (!$haiku) {
                return new JsonResponse(['success' => false, 'message' => 'Haiku non trouvé']);
            }

            try {
                $isLiked = $this->likeService->toggleLike($haiku);
                return new JsonResponse([
                    'success' => true,
                    'liked' => $isLiked,
                ]);
            } catch (\Exception $e) {
                return new JsonResponse([
                    'success' => false, 
                    'message' => $e->getMessage(),
                ]);
            }
        }
}



