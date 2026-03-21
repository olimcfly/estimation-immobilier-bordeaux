<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\View;
use PDO;

final class AdminDashboardController
{
    public function index(): void
    {
        AuthController::requireAuth();

        $stats = [
            'total_leads' => 0,
            'new_leads_today' => 0,
            'hot_leads' => 0,
            'leads_tiede' => 0,
            'leads_froid' => 0,
            'pending_leads' => 0,
            'total_articles' => 0,
            'draft_articles' => 0,
            'pipeline' => [],
            'revenu_gagne' => 0.0,
            'ca_projete' => 0.0,
            'valeur_portefeuille' => 0.0,
            'commission_potentielle' => 0.0,
            'taux_conversion' => 0,
            'funnel' => [],
            'leads_par_mois' => [],
        ];
        $recentLeads = [];
        $dbError = null;

        try {
            $pdo = Database::connection();
            $websiteId = (int) Config::get('website.id', 1);

            // Total leads
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM leads WHERE website_id = :wid AND lead_type = :lt');
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $stats['total_leads'] = (int) $stmt->fetchColumn();

            // New leads today
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM leads WHERE website_id = :wid AND lead_type = :lt AND DATE(created_at) = CURDATE()');
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $stats['new_leads_today'] = (int) $stmt->fetchColumn();

            // Leads par score
            $stmt = $pdo->prepare('SELECT score, COUNT(*) as cnt FROM leads WHERE website_id = :wid AND lead_type = :lt GROUP BY score');
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $scoreData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
            $stats['hot_leads'] = (int) ($scoreData['chaud'] ?? 0);
            $stats['leads_tiede'] = (int) ($scoreData['tiede'] ?? 0);
            $stats['leads_froid'] = (int) ($scoreData['froid'] ?? 0);

            // Leads par statut (pipeline)
            $stmt = $pdo->prepare('SELECT statut, COUNT(*) as cnt FROM leads WHERE website_id = :wid AND lead_type = :lt GROUP BY statut');
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $statutData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
            $stats['pipeline'] = $statutData;
            $stats['pending_leads'] = (int) ($statutData['nouveau'] ?? 0);

            // Article stats
            if (Database::tableExists('articles')) {
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE website_id = :wid');
                $stmt->execute([':wid' => $websiteId]);
                $stats['total_articles'] = (int) $stmt->fetchColumn();

                $stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE website_id = :wid AND status = :st');
                $stmt->execute([':wid' => $websiteId, ':st' => 'draft']);
                $stats['draft_articles'] = (int) $stmt->fetchColumn();
            }

            // CA signe (revenu gagne)
            $stmt = $pdo->prepare('SELECT COALESCE(SUM(commission_montant), 0) FROM leads WHERE website_id = :wid AND statut = :st AND commission_montant IS NOT NULL');
            $stmt->execute([':wid' => $websiteId, ':st' => 'signe']);
            $stats['revenu_gagne'] = (float) $stmt->fetchColumn();

            // CA projete (mandats en cours)
            $stmt = $pdo->prepare('SELECT COALESCE(SUM(commission_montant), 0) FROM leads WHERE website_id = :wid AND statut IN ("mandat_simple","mandat_exclusif","compromis_vente","co_signature_partenaire") AND commission_montant IS NOT NULL');
            $stmt->execute([':wid' => $websiteId]);
            $stats['ca_projete'] = (float) $stmt->fetchColumn();

            // Valeur totale du portefeuille
            $stmt = $pdo->prepare('SELECT COALESCE(SUM(estimation), 0) FROM leads WHERE website_id = :wid AND lead_type = :lt AND statut NOT IN ("assigne_autre")');
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $stats['valeur_portefeuille'] = (float) $stmt->fetchColumn();

            // Commission potentielle totale
            $stmt = $pdo->prepare('SELECT COALESCE(SUM(COALESCE(commission_montant, estimation * COALESCE(commission_taux, 3) / 100)), 0) FROM leads WHERE website_id = :wid AND lead_type = :lt AND statut NOT IN ("assigne_autre","signe")');
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $stats['commission_potentielle'] = (float) $stmt->fetchColumn();

            // Taux de conversion global
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM leads WHERE website_id = :wid AND lead_type = :lt AND statut IN ("signe","co_signature_partenaire")');
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $signes = (int) $stmt->fetchColumn();
            $stats['taux_conversion'] = $stats['total_leads'] > 0 ? round(($signes / $stats['total_leads']) * 100, 1) : 0;

            // Taux par etape (funnel)
            $pipelineOrder = [
                'nouveau', 'contacte', 'rdv_pris', 'visite_realisee',
                'mandat_simple', 'mandat_exclusif', 'compromis_vente',
                'signe', 'co_signature_partenaire',
            ];
            $funnel = [];
            foreach ($pipelineOrder as $step) {
                $funnel[$step] = (int) ($statutData[$step] ?? 0);
            }
            $stats['funnel'] = $funnel;

            // Leads recents
            $stmt = $pdo->prepare('SELECT id, nom, email, telephone, ville, estimation, score, statut, created_at FROM leads WHERE website_id = :wid AND lead_type = :lt ORDER BY created_at DESC LIMIT 10');
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $recentLeads = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // Leads par mois (6 derniers mois)
            $stmt = $pdo->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') as mois, COUNT(*) as cnt FROM leads WHERE website_id = :wid AND lead_type = :lt AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY mois ORDER BY mois ASC");
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $stats['leads_par_mois'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        } catch (\Throwable $e) {
            error_log('Admin dashboard DB error: ' . $e->getMessage());
            $dbError = 'Base de données indisponible. Vérifiez la connexion dans la page Diagnostic.';
        }

        View::renderAdmin('admin/dashboard', [
            'page_title' => 'Tableau de Bord - Admin CRM',
            'admin_page_title' => 'Tableau de bord',
            'admin_page' => 'dashboard',
            'breadcrumb' => 'Tableau de Bord',
            'stats' => $stats,
            'recent_leads' => $recentLeads,
            'dbError' => $dbError,
        ]);
    }

    public function funnel(): void
    {
        AuthController::requireAuth();

        $pipelineData = [];
        $total = 0;
        $totalValeur = 0;
        $scoreData = [];
        $scoreValeurs = [];
        $tendanceCount = 0;
        $monthlyData = [];
        $dbError = null;

        try {
            $pdo = Database::connection();
            $websiteId = (int) Config::get('website.id', 1);

            // Full pipeline data with estimation values and commission
            $stmt = $pdo->prepare(
                'SELECT statut, COUNT(*) as cnt, COALESCE(SUM(estimation), 0) as valeur,
                        COALESCE(SUM(CASE WHEN commission_montant IS NOT NULL THEN commission_montant ELSE COALESCE(estimation, 0) * COALESCE(commission_taux, 3) / 100 END), 0) as commission
                 FROM leads WHERE website_id = :wid AND lead_type = :lt GROUP BY statut'
            );
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pipelineData[$row['statut']] = [
                    'count' => (int) $row['cnt'],
                    'valeur' => (float) $row['valeur'],
                    'commission' => (float) $row['commission'],
                ];
            }

            foreach ($pipelineData as $d) {
                $total += $d['count'];
                $totalValeur += $d['valeur'];
            }

            // Leads by score for the funnel
            $stmt = $pdo->prepare('SELECT score, COUNT(*) as cnt FROM leads WHERE website_id = :wid AND lead_type = :lt GROUP BY score');
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $scoreData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

            // Score distribution with values
            $stmt = $pdo->prepare(
                'SELECT score, COALESCE(SUM(estimation), 0) as valeur
                 FROM leads WHERE website_id = :wid AND lead_type = :lt GROUP BY score'
            );
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $scoreValeurs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

            // Tendance leads count
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM leads WHERE website_id = :wid AND lead_type = :lt');
            $stmt->execute([':wid' => $websiteId, ':lt' => 'tendance']);
            $tendanceCount = (int) $stmt->fetchColumn();

            // Monthly conversion trend (last 6 months)
            $stmt = $pdo->prepare(
                "SELECT DATE_FORMAT(created_at, '%Y-%m') as mois, statut, COUNT(*) as cnt
                 FROM leads WHERE website_id = :wid AND lead_type = :lt
                 AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                 GROUP BY mois, statut ORDER BY mois ASC"
            );
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            error_log('Funnel DB error: ' . $e->getMessage());
            $dbError = 'Erreur base de données. Vérifiez la connexion dans la page Diagnostic.';
        }

        View::renderAdmin('admin/funnel', [
            'page_title' => 'Entonnoir de Vente - Admin CRM',
            'admin_page_title' => 'Funnel de vente',
            'admin_page' => 'funnel',
            'breadcrumb' => 'Entonnoir de Vente',
            'pipelineData' => $pipelineData,
            'scoreData' => $scoreData,
            'scoreValeurs' => $scoreValeurs,
            'tendanceCount' => $tendanceCount,
            'total' => $total,
            'totalValeur' => $totalValeur,
            'monthlyData' => $monthlyData,
            'dbError' => $dbError,
        ]);
    }

    public function portfolio(): void
    {
        AuthController::requireAuth();

        $leads = [];
        $totalValeur = 0;
        $totalCommission = 0;
        $defaultRate = (float) Config::get('portfolio.default_commission_rate', 3.0);
        $dbError = null;

        try {
            $pdo = Database::connection();
            $websiteId = (int) Config::get('website.id', 1);

            // Check if partenaires table exists before attempting JOIN
            $hasPartenaires = Database::tableExists('partenaires');
            $hasPartenaireId = false;
            if ($hasPartenaires) {
                $colCheck = $pdo->prepare(
                    'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c'
                );
                $colCheck->execute([':t' => 'leads', ':c' => 'partenaire_id']);
                $hasPartenaireId = (int) $colCheck->fetchColumn() > 0;
            }

            // Active leads with portfolio data
            if ($hasPartenaires && $hasPartenaireId) {
                $stmt = $pdo->prepare('SELECT l.*, p.nom as partenaire_nom, p.entreprise as partenaire_entreprise
                        FROM leads l
                        LEFT JOIN partenaires p ON l.partenaire_id = p.id
                        WHERE l.website_id = :wid AND l.lead_type = :lt AND l.statut NOT IN ("assigne_autre")
                        ORDER BY l.estimation DESC');
            } else {
                $stmt = $pdo->prepare('SELECT l.*, NULL as partenaire_nom, NULL as partenaire_entreprise
                        FROM leads l
                        WHERE l.website_id = :wid AND l.lead_type = :lt AND l.statut NOT IN ("assigne_autre")
                        ORDER BY l.estimation DESC');
            }
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $leads = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // Calculate totals
            foreach ($leads as &$lead) {
                $taux = (float) ($lead['commission_taux'] ?? $defaultRate);
                $estimation = (float) $lead['estimation'];
                $commission = $lead['commission_montant'] ? (float) $lead['commission_montant'] : ($estimation * $taux / 100);
                $lead['commission_calculee'] = $commission;
                $lead['commission_taux_effectif'] = $taux;
                $totalValeur += $estimation;
                $totalCommission += $commission;
            }
            unset($lead);
        } catch (\Throwable $e) {
            error_log('Portfolio DB error: ' . $e->getMessage());
            $dbError = 'Base de données indisponible. Vérifiez la connexion dans la page Diagnostic.';
        }

        View::renderAdmin('admin/portfolio', [
            'page_title' => 'Portefeuille Client - Admin CRM',
            'admin_page_title' => 'Portfolio',
            'admin_page' => 'portfolio',
            'breadcrumb' => 'Portefeuille Client',
            'leads' => $leads,
            'totalValeur' => $totalValeur,
            'totalCommission' => $totalCommission,
            'defaultRate' => $defaultRate,
            'dbError' => $dbError,
        ]);
    }

    public function updateCommissionRate(): void
    {
        AuthController::requireAuth();

        header('Content-Type: application/json; charset=utf-8');

        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        if ($sessionToken === '' || $csrfToken === '' || !hash_equals($sessionToken, $csrfToken)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Token CSRF invalide']);
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $rate = (float) ($_POST['commission_taux'] ?? 0);

        if ($id <= 0 || $rate < 0 || $rate > 20) {
            echo json_encode(['success' => false, 'error' => 'Paramètres invalides']);
            return;
        }

        $lead = new \App\Models\Lead();
        $updated = $lead->updateLeadDetails($id, ['commission_taux' => $rate]);

        echo json_encode(['success' => $updated]);
    }
}
