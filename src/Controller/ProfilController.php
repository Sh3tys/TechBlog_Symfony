<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ChangePasswordType;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profil')]
#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractController
{
    /**
     * Afficher le profil de l'utilisateur connecté
     */
    #[Route('/', name: 'app_profil')]
    public function index(): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        return $this->render('profil/index.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    /**
     * Modifier les informations du profil (email, pseudo)
     */
    #[Route('/modifier', name: 'app_profil_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        $form = $this->createForm(ProfilType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été modifié avec succès !');

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/edit.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Changer le mot de passe
     */
    #[Route('/modifier-mot-de-passe', name: 'app_profil_change_password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que le mot de passe actuel est correct
            $currentPassword = $form->get('currentPassword')->getData();

            if (!$passwordHasher->isPasswordValid($utilisateur, $currentPassword)) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                return $this->redirectToRoute('app_profil_change_password');
            }

            // Hasher et enregistrer le nouveau mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $utilisateur->setPassword(
                $passwordHasher->hashPassword($utilisateur, $plainPassword)
            );

            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès !');

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/change_password.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Supprimer le compte utilisateur
     */
    #[Route('/supprimer', name: 'app_profil_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            // Déconnecter l'utilisateur
            $this->container->get('security.token_storage')->setToken(null);

            // Supprimer le compte
            $entityManager->remove($utilisateur);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été supprimé avec succès.');

            return $this->redirectToRoute('app_accueil');
        }

        $this->addFlash('error', 'Une erreur est survenue lors de la suppression du compte.');
        return $this->redirectToRoute('app_profil');
    }

    /**
     * Changer le rôle utilisateur (DEV)
     */
    #[Route('/basculer-role', name: 'app_profil_toggle_role', methods: ['POST'])]
    public function toggleRole(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        // Vérification CSRF
        if ($this->isCsrfTokenValid('toggle_role', $request->request->get('_token'))) {
            // Rôles actuels
            $roles = $utilisateur->getRoles();

            // Changement de rôle
            if (in_array('ROLE_ADMIN', $roles)) {
                // Passage en utilisateur
                $utilisateur->setRoles(['ROLE_USER']);
                $this->addFlash('info', 'Vous êtes maintenant utilisateur.');
            } else {
                // Passage en administrateur
                $utilisateur->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
                $this->addFlash('success', 'Vous êtes maintenant administrateur.');
            }

            $entityManager->flush();

            // Rafraîchir la session
            $this->container->get('security.token_storage')->setToken(null);
        }

        return $this->redirectToRoute('app_profil');
    }

}
