<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response
    {
        // Rediriger si déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_accueil');
        }

        $utilisateur = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le mot de passe
            $utilisateur->setPassword(
                $userPasswordHasher->hashPassword(
                    $utilisateur,
                    $form->get('plainPassword')->getData()
                )
            );

            // Marquer comme vérifié automatiquement
            $utilisateur->setIsVerified(true);

            // Sauvegarder en base de données
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            // Envoyer l'email de bienvenue
            $this->envoyerEmailBienvenue($utilisateur, $mailer);

            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * Envoie un email de bienvenue à l'utilisateur
     */
    private function envoyerEmailBienvenue(Utilisateur $utilisateur, MailerInterface $mailer): void
    {
        try {
            $email = (new TemplatedEmail())
                ->from(new Address('noreply@techblog-gaming.fr', 'TechBlog Gaming'))
                ->to($utilisateur->getEmail())
                ->subject('Bienvenue sur TechBlog Gaming')
                ->htmlTemplate('emails/bienvenue.html.twig')
                ->context(['utilisateur' => $utilisateur]);

            $mailer->send($email);

            $this->addFlash('success', 'Votre compte a été créé avec succès ! Un email de bienvenue vous a été envoyé.');
        } catch (\Exception $e) {
            $this->addFlash('success', 'Votre compte a été créé avec succès !');
        }
    }
}
