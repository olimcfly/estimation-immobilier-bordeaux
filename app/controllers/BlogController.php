<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;

final class BlogController
{
    public function index(): void
    {
        try {
            $articleModel = new Article();
            $articles = $articleModel->findPublished();
        } catch (\Throwable $e) {
            error_log('Blog index error: ' . $e->getMessage());
            $articles = [];
        }

        View::render('blog/index', ['articles' => $articles]);
    }

    public function show(string $slug): void
    {
        try {
            $articleModel = new Article();
            $article = $articleModel->findBySlug($slug);
        } catch (\Throwable $e) {
            error_log('Blog show error: ' . $e->getMessage());
            $article = null;
        }

        if ($article === null) {
            http_response_code(404);
            echo 'Article introuvable';
            return;
        }

        View::render('blog/show', ['article' => $article]);
    }
}
