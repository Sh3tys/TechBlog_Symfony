<?php

namespace App\Controller;

use App\Entity\MessageContact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response
    {
        // Créer une nouvelle instance de MessageContact
        $messageContact = new MessageContact();

        // Créer le formulaire
        $form = $this->createForm(ContactType::class, $messageContact);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder le message en base de données
            $entityManager->persist($messageContact);
            $entityManager->flush();

            // Envoyer l'email via Mailtrap
            $email = (new TemplatedEmail())
                ->from(new Address('contact@techblog-gaming.fr', 'TechBlog Gaming'))
                ->to('admin@techblog-gaming.fr')
                ->subject('Nouveau message de contact')
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'messageContact' => $messageContact,
                ]);

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.');
            } catch (\Exception $e) {
                // Si l'email échoue (Mailtrap pas configuré), on informe quand même l'utilisateur
                $this->addFlash('success', 'Votre message a été enregistré avec succès ! Nous vous répondrons dans les plus brefs délais.');
            }

            return $this->redirectToRoute('app_contact');
        }

        // Afficher le formulaire
        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
