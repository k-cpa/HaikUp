<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

final class PasswordResetController extends AbstractController
{
    #[Route('/reset/password', name: 'app_forgot_password')]
    public function request(Request $request, UserRepository $usersRepository, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {

        $form = $this->createForm(ResetPasswordRequestType::class); // Création du form
        $form->handleRequest($request); // Récupération de la requête

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $usersRepository->findOneBy(['email' => $email]);

            if($user) {
                $token = Uuid::v4()->toRfc4122(); // Universally Unique Identifier permet de créer un Token unique
                $user->setResetToken($token);

                $user->setResetTokenExpiresAt((new \DateTime('now', new \DateTimeZone('Europe/Paris')))->modify('+1 hour'));
                // On stocke la date d'expiration du token dans l'objet user
                $entityManager->flush();

                $resetLink = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                // Création du lien intégrant le token
                $email = (new Email()) // création du mail
                    ->from('contact@kevcampana.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->text("Voici votre lien de réinitialisation : $resetLink");
                    // Intégration du lien dans le mail 
                    $mailer->send($email);
                    // Envoi du mail
                    $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');

                    return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Aucun utilisateur trouvé avec l\'adresse mail renseignée');
        }

        return $this->render('password_reset/passwordRequest.html.twig', [
            'resetRequestForm' => $form->createView(),
        ]);
    }

    #[Route('/{token}/reset-password', name: 'app_reset_password')]
    public function reset(string $token, Request $request, UserRepository $usersRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHash): Response
    {
        $user = $usersRepository->findOneBy(['resetToken' => $token]); // Requete DQL pour récupérer l'objet User associé au token
        if (!$user || !$user->isResetTokenValid()) {
            // On vérifie si l'objet User n'a pas été trouvé ou si le token est invalide (via méthode créé dans le User)
            $this->addFlash('danger', 'Ce lien de réinitialisation est invalide ou expiré');
            return $this->redirectToRoute('app_forgot_password');
        }

        $resetForm = $this->createForm(ResetPasswordType::class);
        $resetForm->handleRequest($request);

        if ($resetForm->isSubmitted() && $resetForm->isValid()) {
            $password = $resetForm->get('password')->getData();
            $hashedPassword = $passwordHash->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);



            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('password_reset/passwordReset.html.twig', [
            'resetForm' => $resetForm->createView(),
        ]);
    }
}