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

        $pdo = Database::connection();
        $websiteId = (int) Config::get('website.id', 1);
        $stats = [];

        // Total contacts
        $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM leads WHERE website_id = :wid AND lead_type = :lt');
        $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
        $stats['total_contacts'] = (int) $stmt->fetchColumn();

        // Leads par score
        $stmt = $pdo->prepare('SELECT score, COUNT(*) as cnt FROM leads WHERE website_id = :wid AND lead_type = :lt GROUP BY score');
        $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
        $scoreData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
        $stats['leads_chaud'] = (int) ($scoreData['chaud'] ?? 0);
        $stats['leads_tiede'] = (int) ($scoreData['tiede'] ?? 0);
        $stats['leads_froid'] = (int) ($scoreData['froid'] ?? 0);

        // Leads par statut (pipeline)
        $stmt = $pdo->prepare('SELECT statut, COUNT(*) as cnt FROM leads WHERE website_id = :wid AND lead_type = :lt GROUP BY statut');
        $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
        $statutData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
        $stats['pipeline'] = $statutData;

        // CA signé (revenu gagné)
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(commission_montant), 0) as total FROM leads WHERE website_id = :wid AND statut = :st AND commission_montant IS NOT NULL');
        $stmt->execute([':wid' => $websiteId, ':st' => 'signe']);
        $stats['revenu_gagne'] = (float) $stmt->fetchColumn();

        // CA projeté (mandats en cours)
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(commission_montant), 0) as total FROM leads WHERE website_id = :wid AND statut IN ("mandat_simple","mandat_exclusif","compromis_vente","co_signature_partenaire") AND commission_montant IS NOT NULL');
        $stmt->execute([':wid' => $websiteId]);
        $stats['ca_projete'] = (float) $stmt->fetchColumn();

        // Valeur totale du portefeuille (estimations des leads actifs)
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(estimation), 0) as total FROM leads WHERE website_id = :wid AND lead_type = :lt AND statut NOT IN ("assigne_autre")');
        $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
        $stats['valeur_portefeuille'] = (float) $stmt->fetchColumn();

        // Commission potentielle totale
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(COALESCE(commission_montant, estimation * COALESCE(commission_taux, 3) / 100)), 0) as total FROM leads WHERE website_id = :wid AND lead_type = :lt AND statut NOT IN ("assigne_autre","signe")');
        $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
        $stats['commission_potentielle'] = (float) $stmt->fetchColumn();

        // Taux de conversion global
        $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM leads WHERE website_id = :wid AND lead_type = :lt AND statut IN ("signe","co_signature_partenaire")');
        $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
        $signes = (int) $stmt->fetchColumn();
        $stats['taux_conversion'] = $stats['total_contacts'] > 0 ? round(($signes / $stats['total_contacts']) * 100, 1) : 0;

        // Taux par étape (funnel)
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

        // Leads récents
        $stmt = $pdo->prepare('SELECT id, nom, email, telephone, ville, estimation, score, statut, created_at FROM leads WHERE website_id = :wid AND lead_type = :lt ORDER BY created_at DESC LIMIT 10');
        $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
        $stats['leads_recents'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Leads par mois (6 derniers mois)
        $stmt = $pdo->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') as mois, COUNT(*) as cnt FROM leads WHERE website_id = :wid AND lead_type = :lt AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY mois ORDER BY mois ASC");
        $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
        $stats['leads_par_mois'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        View::renderAdmin('admin/dashboard', [
            'page_title' => 'Tableau de Bord - Admin CRM',
            'admin_page' => 'dashboard',
            'breadcrumb' => 'Tableau de Bord',
            'stats' => $stats,
        ]);
    }

    public function funnel(): void
    {
        AuthController::requireAuth();

        $pdo = Database::connection();
        $websiteId = (int) Config::get('website.id', 1);

        // Full pipeline data
        $stmt = $pdo->prepare('SELECT statut, COUNT(*) as cnt, COALESCE(SUM(estimation), 0) as valeur FROM leads WHERE website_id = :wid AND lead_type = :lt GROUP BY statut');
        $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
        $pipelineData = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pipelineData[$row['statut']] = ['count' => (int) $row['cnt'], 'valeur' => (float) $row['valeur']];
        }

        $total = 0;
        foreach ($pipelineData as $d) {
            $total += $d['count'];
        }

        // Leads by score for the funnel
        $stmt = $pdo->prepare('SELECT score, COUNT(*) as cnt FROM leads WHERE website_id = :wid AND lead_type = :lt GROUP BY score');
        $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
        $scoreData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        // Tendance leads count
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM leads WHERE website_id = :wid AND lead_type = :lt');
        $stmt->execute([':wid' => $websiteId, ':lt' => 'tendance']);
        $tendanceCount = (int) $stmt->fetchColumn();

        View::renderAdmin('admin/funnel', [
            'page_title' => 'Entonnoir de Vente - Admin CRM',
            'admin_page' => 'funnel',
            'breadcrumb' => 'Entonnoir de Vente',
            'pipelineData' => $pipelineData,
            'scoreData' => $scoreData,
            'tendanceCount' => $tendanceCount,
            'total' => $total,
        ]);
    }

    public function portfolio(): void
    {
        AuthController::requireAuth();

        $pdo = Database::connection();
        $websiteId = (int) Config::get('website.id', 1);

        // Active leads with portfolio data
        $stmt = $pdo->prepare('SELECT l.*, p.nom as partenaire_nom, p.entreprise as partenaire_entreprise
                FROM leads l
                LEFT JOIN partenaires p ON l.partenaire_id = p.id
                WHERE l.website_id = :wid AND l.lead_type = :lt AND l.statut NOT IN ("assigne_autre")
                ORDER BY l.estimation DESC');
        $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
        $leads = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Calculate totals
        $totalValeur = 0;
        $totalCommission = 0;
        foreach ($leads as &$lead) {
            $taux = (float) ($lead['commission_taux'] ?? 3.0);
            $estimation = (float) $lead['estimation'];
            $commission = $lead['commission_montant'] ? (float) $lead['commission_montant'] : ($estimation * $taux / 100);
            $lead['commission_calculee'] = $commission;
            $lead['commission_taux_effectif'] = $taux;
            $totalValeur += $estimation;
            $totalCommission += $commission;
        }
        unset($lead);

        View::renderAdmin('admin/portfolio', [
            'page_title' => 'Portefeuille Client - Admin CRM',
            'admin_page' => 'portfolio',
            'breadcrumb' => 'Portefeuille Client',
            'leads' => $leads,
            'totalValeur' => $totalValeur,
            'totalCommission' => $totalCommission,
        ]);
    }
}
