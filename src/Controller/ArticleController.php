<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/article')]
class ArticleController extends AbstractController
{
    /**
     * Liste publique de tous les articles publiés
     */
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        // Récupérer uniquement les articles publiés, triés par date décroissante
        $articles = $articleRepository->findBy(
            ['publie' => true],
            ['dateCreation' => 'DESC']
        );

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * Afficher un article spécifique (accessible à tous)
     */
    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        // Vérifier que l'article est publié (sauf pour les admins)
        if (!$article->isPublie() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Cet article n\'est pas encore publié.');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * Créer un nouvel article (ADMIN uniquement)
     */
    #[Route('/nouveau/article', name: 'app_article_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'article à l'utilisateur connecté
            $article->setAuteur($this->getUser());

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé avec succès !');

            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * Modifier un article existant (ADMIN uniquement)
     */
    #[Route('/{id}/modifier', name: 'app_article_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Article modifié avec succès !');

            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * Supprimer un article (ADMIN uniquement)
     */
    #[Route('/{id}/supprimer', name: 'app_article_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        // Vérification du token CSRF pour la sécurité
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article supprimé avec succès !');
        }

        return $this->redirectToRoute('app_admin_articles');
    }

    /**
     * Panel d'administration des articles (ADMIN uniquement)
     */
    #[Route('/admin/gestion', name: 'app_admin_articles', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function admin(ArticleRepository $articleRepository): Response
    {
        // Récupérer TOUS les articles (publiés et non publiés)
        $articles = $articleRepository->findBy([], ['dateCreation' => 'DESC']);

        return $this->render('article/admin.html.twig', [
            'articles' => $articles,
        ]);
    }
}
