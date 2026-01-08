<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(ArticleRepository $articleRepository): Response
    {
        // Récupérer les 6 derniers articles publiés
        $articles = $articleRepository->findBy(
            ['publie' => true],
            ['dateCreation' => 'DESC'],
            6
        );

        return $this->render('accueil/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
