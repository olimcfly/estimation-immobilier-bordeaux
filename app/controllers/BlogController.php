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

        View::render('blog/index', ['articles' => $articles]);
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

        View::render('blog/show', ['article' => $article]);
    }
}
