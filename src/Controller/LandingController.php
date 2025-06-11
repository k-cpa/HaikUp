<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LandingController extends AbstractController
{
    #[Route('/', name: 'app_landing')]
    public function landing(): Response
    {
        return $this->render('landing/index.html.twig', [
            'controller_name' => 'LandingController',
        ]);
    }

    #[Route('/confidentialités', name: 'app_confidentiality')]
    public function confidentiality(): Response
    {
        return $this->render('landing/confidentiality.html.twig', [
            'controller_name' => 'LandingController',
        ]);
    }
}
