<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;
use App\Models\Lead;

final class AdminController
{
    public function dashboard(): void
    {
        AuthController::requireAuth();

        $stats = [
            'total_leads' => 0,
            'hot_leads' => 0,
            'pending_leads' => 0,
            'new_leads_today' => 0,
            'total_articles' => 0,
            'draft_articles' => 0,
        ];

        $recentLeads = [];

        try {
            $leadModel = new Lead();
            $allLeads = $leadModel->findAllLeads();

            $stats['total_leads'] = count($allLeads);

            $today = date('Y-m-d');
            foreach ($allLeads as $lead) {
                if (($lead['score'] ?? '') === 'chaud') {
                    $stats['hot_leads']++;
                }
                if (($lead['statut'] ?? '') === 'nouveau') {
                    $stats['pending_leads']++;
                }
                if (str_starts_with((string) ($lead['created_at'] ?? ''), $today)) {
                    $stats['new_leads_today']++;
                }
            }

            $recentLeads = array_slice($allLeads, 0, 5);
        } catch (\Throwable $e) {
            error_log('Admin dashboard leads error: ' . $e->getMessage());
        }

        try {
            $articleModel = new Article();
            $allArticles = $articleModel->findAll();
            $stats['total_articles'] = count($allArticles);

            foreach ($allArticles as $article) {
                if (($article['status'] ?? '') === 'draft') {
                    $stats['draft_articles']++;
                }
            }
        } catch (\Throwable $e) {
            error_log('Admin dashboard articles error: ' . $e->getMessage());
        }

        View::renderAdmin('admin/dashboard', [
            'page_title' => 'Tableau de bord - Admin',
            'admin_page_title' => 'Tableau de bord',
            'admin_current_page' => 'dashboard',
            'stats' => $stats,
            'recent_leads' => $recentLeads,
        ]);
    }
}
