<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/landing/contact', name: 'app_contact')]
    public function contactForm(Request $request, MailerInterface $mailer): Response
    {

        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);

        if($contactForm->isSubmitted() && $contactForm->isValid()) {
            $data = $contactForm->getData(); // Récupération des champs du formulaire

            $email = (new Email())
                ->from('contact@kevcampana.com')
                ->to('kevcampana@gmail.com')
                ->subject('Nouveau message de contact')
                ->text(
                    "Nom de la personne à l'origine du contact: {$data['nom']}\n".
                    "Email de contact sélectionné : {$data['email']}\n\n".
                    "Message:\n/n{$data['message']}"
                );
                $mailer->send($email);

            return $this->redirectToRoute('app_feed');

                
        }
        return $this->render('landing/contact.html.twig', [
            'contactForm' => $contactForm->createView(),
        ]);
    }
}
