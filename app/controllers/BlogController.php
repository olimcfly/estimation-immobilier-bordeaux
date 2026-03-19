<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;

final class BlogController
{
    public function index(): void
    {
        $articleModel = new Article();
        $articles = $articleModel->findPublished();

        View::render('blog/index', [
            'articles' => $articles,
            'metaTitle' => 'Blog immobilier Bordeaux | Conseils vendeurs',
            'metaDescription' => 'Guides pratiques pour vendre votre appartement ou maison à Bordeaux dans les meilleures conditions.',
        ]);
    }

    public function show(string $slug): void
    {
        $articleModel = new Article();
        $article = $articleModel->findBySlug($slug);

        if ($article === null) {
            http_response_code(404);
            echo 'Article introuvable';
            return;
        }

        View::render('blog/show', [
            'article' => $article,
            'metaTitle' => (string) $article['meta_title'],
            'metaDescription' => (string) $article['meta_description'],
        ]);
    }
}
