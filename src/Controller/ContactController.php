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
                    "<br>Informations de contact</br>\n\n".
                    "<br>Nom de la personne :</br> {$data['nom']}\n".
                    "<br>Email de contact :</br> {$data['email']}\n\n".
                    "<br>Message:</br>\n\n{$data['message']}"
                );
                $mailer->send($email);

            $referer = $request->headers->get('referer');

            // Si il y a bien une URL précédente, on redirige vers elle, sinon vers une route par défaut
            if ($this->getUser()) {
                return $this->redirectToRoute('app_feed');
            } else {
                return $this->redirectToRoute('app_landing');
            }
          
        }
        return $this->render('landing/contact.html.twig', [
            'contactForm' => $contactForm->createView(),
        ]);
    }
}
