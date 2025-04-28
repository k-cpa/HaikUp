<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignUpType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

// Gestion Controller Création du compte

    #[Route(path: '/signup', name: 'app_signup')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHash): Response
    {
       
       $user = new User();

       $formSignUp = $this->createForm(SignUpType::class, $user);
       $formSignUp->handleRequest($request);

       if ($formSignUp->isSubmitted() && $formSignUp->isValid()) {

            $user->setRoles(['ROLE_USER']); 
            $user->setPassword(
                $passwordHash->hashPassword($user,$formSignUp->get('password')->getData())
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Compte enregistré');
            return $this->redirectToRoute('app_login');
       }

       return $this->render('security/signup.html.twig', [
        'formSignUp' => $formSignUp->createView(),
       ]);
    }

// Gestion Controller Connexion du compte

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
