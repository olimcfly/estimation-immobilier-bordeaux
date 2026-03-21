<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\View;
use App\Services\Mailer;

final class AdminDiagnosticController
{
    public function index(): void
    {
        AuthController::requireAuth();

        // 1. Fichier .env
        $envFile = dirname(__DIR__, 2) . '/.env';
        $envExists = is_file($envFile);

        // 2. Configuration DB
        $dbConfig = [
            'host' => Config::get('db.host', '(non défini)'),
            'port' => Config::get('db.port', '(non défini)'),
            'name' => Config::get('db.name', '(non défini)'),
            'user' => Config::get('db.user', '(non défini)'),
        ];
        $dbPassDefined = Config::get('db.pass', '') !== '';

        // 3. Connexion DB
        $dbConnected = false;
        $dbVersion = '';
        $dbError = '';
        $pdo = null;
        try {
            $pdo = Database::connection();
            $dbConnected = true;
            $dbVersion = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION) ?: '';
        } catch (\Throwable $e) {
            $dbError = $e->getMessage();
        }

        // 4. Tables
        $tables = [];
        if ($dbConnected && $pdo !== null) {
            try {
                $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // 5. Admin users
        $adminTableOk = false;
        $adminCount = 0;
        $adminColumns = [];
        $loginCodeExists = false;
        $adminEmails = [];
        if ($dbConnected && $pdo !== null && in_array('admin_users', $tables, true)) {
            $adminTableOk = true;
            try {
                $adminColumns = $pdo->query('SHOW COLUMNS FROM admin_users')->fetchAll(\PDO::FETCH_COLUMN);
                $adminCount = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
                $loginCodeExists = in_array('login_code', $adminColumns, true);
                $rows = $pdo->query('SELECT email FROM admin_users')->fetchAll(\PDO::FETCH_COLUMN);
                $adminEmails = $rows ?: [];
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // 6. SMTP Configuration
        $smtpHost = (string) Config::get('mail.smtp_host');
        $smtpPort = (int) Config::get('mail.smtp_port', 587);
        $smtpUser = (string) Config::get('mail.smtp_user');
        $smtpPass = (string) Config::get('mail.smtp_pass');
        $smtpEncryption = (string) Config::get('mail.smtp_encryption', 'tls');
        $smtpFrom = (string) Config::get('mail.from', '');
        $smtpPassDefined = $smtpPass !== '';
        $smtpConfigured = $smtpHost !== '' && $smtpUser !== '' && $smtpPass !== '';

        // 7. Test SMTP connection
        $smtpConnected = false;
        $smtpError = '';
        $smtpDiagnostics = [];
        $smtpAdvice = '';
        if ($smtpConfigured) {
            try {
                if (class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $smtpHost;
                    $mail->Port = $smtpPort;
                    $mail->SMTPAuth = true;
                    $mail->Username = $smtpUser;
                    $mail->Password = $smtpPass;
                    $mail->Timeout = 10;
                    $mail->SMTPDebug = 0;

                    if ($smtpPort === 465) {
                        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                    } elseif ($smtpEncryption === 'tls' || $smtpPort === 587) {
                        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    } else {
                        $mail->SMTPSecure = $smtpEncryption;
                    }
                    $mail->AuthType = '';

                    $mail->smtpConnect();
                    $mail->smtpClose();
                    $smtpConnected = true;
                } else {
                    $smtpError = 'PHPMailer non installé — Exécutez "composer install"';
                }
            } catch (\Throwable $e) {
                $smtpError = $e->getMessage();
                $smtpDiagnostics = Mailer::diagnose(['error_message' => $smtpError]);
            }
        }

        // 8. Collect issues
        $issues = [];
        if (!$envExists) {
            $issues[] = 'Le fichier .env est absent.';
        }
        if (!$dbPassDefined) {
            $issues[] = 'Le mot de passe de la base de données est vide.';
        }
        if (!$dbConnected) {
            $issues[] = 'Impossible de se connecter à la base de données.';
        }
        if ($dbConnected && !$adminTableOk) {
            $issues[] = 'La table admin_users est absente.';
        }
        if ($adminTableOk && !$loginCodeExists) {
            $issues[] = 'La colonne login_code est manquante dans admin_users.';
        }
        if ($smtpConfigured && !$smtpConnected) {
            $issues[] = 'La connexion SMTP a échoué.';
        }

        // 9. DEV_SKIP_AUTH status
        $devSkipAuth = AuthController::isLoggedIn() && filter_var(
            $_ENV['DEV_SKIP_AUTH'] ?? $_SERVER['DEV_SKIP_AUTH'] ?? 'false',
            FILTER_VALIDATE_BOOLEAN
        );

        View::renderAdmin('admin/diagnostic', [
            'page_title' => 'Diagnostic - Admin',
            'admin_page_title' => 'Diagnostic',
            'admin_page' => 'diagnostic',
            'breadcrumb' => 'Diagnostic',
            'devSkipAuth' => $devSkipAuth,
            'envExists' => $envExists,
            'dbConfig' => $dbConfig,
            'dbPassDefined' => $dbPassDefined,
            'dbConnected' => $dbConnected,
            'dbVersion' => $dbVersion,
            'dbError' => $dbError,
            'tables' => $tables,
            'adminTableOk' => $adminTableOk,
            'adminCount' => $adminCount,
            'adminColumns' => $adminColumns,
            'loginCodeExists' => $loginCodeExists,
            'adminEmails' => $adminEmails,
            'smtpHost' => $smtpHost,
            'smtpPort' => $smtpPort,
            'smtpUser' => $smtpUser,
            'smtpPassDefined' => $smtpPassDefined,
            'smtpEncryption' => $smtpEncryption,
            'smtpFrom' => $smtpFrom,
            'smtpConfigured' => $smtpConfigured,
            'smtpConnected' => $smtpConnected,
            'smtpError' => $smtpError,
            'smtpDiagnostics' => $smtpDiagnostics,
            'smtpAdvice' => $smtpAdvice,
            'issues' => $issues,
        ]);
    }

    /**
     * Diagnostic base de données : pour chaque page, affiche les tables/colonnes
     * connectées, manquantes ou à créer.
     */
    public function databaseDiagnostic(): void
    {
        AuthController::requireAuth();

        // Définition du schéma attendu par page/module
        $pageSchemas = self::getExpectedPageSchemas();

        // Connexion DB et récupération des tables/colonnes réelles
        $dbConnected = false;
        $pdo = null;
        $existingTables = [];
        $existingColumns = [];

        try {
            $pdo = Database::connection();
            $dbConnected = true;

            $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
            foreach ($tables as $table) {
                $existingTables[] = $table;
                $cols = $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                $existingColumns[$table] = [];
                foreach ($cols as $col) {
                    $existingColumns[$table][$col['Field']] = [
                        'type' => $col['Type'],
                        'null' => $col['Null'],
                        'key' => $col['Key'],
                        'default' => $col['Default'],
                    ];
                }
            }
        } catch (\Throwable $e) {
            // On continue avec un état vide
        }

        // Analyse par page
        $pageResults = [];
        $totalOk = 0;
        $totalWarning = 0;
        $totalError = 0;

        foreach ($pageSchemas as $pageKey => $pageDef) {
            $result = [
                'label' => $pageDef['label'],
                'route' => $pageDef['route'],
                'icon' => $pageDef['icon'],
                'category' => $pageDef['category'],
                'tables' => [],
                'status' => 'ok',
            ];

            if (empty($pageDef['tables'])) {
                $result['status'] = 'no_db';
                $result['message'] = 'Aucune base de donn&eacute;es requise (page statique)';
            } else {
                $hasError = false;
                $hasWarning = false;

                foreach ($pageDef['tables'] as $tableName => $tableDef) {
                    $tableResult = [
                        'name' => $tableName,
                        'description' => $tableDef['description'] ?? '',
                        'exists' => in_array($tableName, $existingTables, true),
                        'columns' => [],
                        'missing_columns' => [],
                        'extra_columns' => [],
                        'status' => 'ok',
                    ];

                    if (!$tableResult['exists']) {
                        $tableResult['status'] = 'error';
                        $tableResult['missing_columns'] = array_keys($tableDef['columns']);
                        $hasError = true;
                    } else {
                        $actualCols = $existingColumns[$tableName] ?? [];

                        foreach ($tableDef['columns'] as $colName => $colDef) {
                            $colExists = isset($actualCols[$colName]);
                            $tableResult['columns'][$colName] = [
                                'expected' => $colDef,
                                'exists' => $colExists,
                                'actual_type' => $colExists ? $actualCols[$colName]['type'] : null,
                            ];
                            if (!$colExists) {
                                $tableResult['missing_columns'][] = $colName;
                                $hasWarning = true;
                            }
                        }

                        if (!empty($tableResult['missing_columns'])) {
                            $tableResult['status'] = 'warning';
                        }
                    }

                    $result['tables'][$tableName] = $tableResult;
                }

                if ($hasError) {
                    $result['status'] = 'error';
                    $totalError++;
                } elseif ($hasWarning) {
                    $result['status'] = 'warning';
                    $totalWarning++;
                } else {
                    $totalOk++;
                }
            }

            $pageResults[$pageKey] = $result;
        }

        // Grouper par catégorie
        $categories = [];
        foreach ($pageResults as $key => $page) {
            $cat = $page['category'];
            if (!isset($categories[$cat])) {
                $categories[$cat] = [];
            }
            $categories[$cat][$key] = $page;
        }

        View::renderAdmin('admin/diagnostic-database', [
            'page_title' => 'Diagnostic Base de Donn&eacute;es - Admin',
            'admin_page_title' => 'Diagnostic BDD',
            'admin_page' => 'diagnostic',
            'breadcrumb' => 'Diagnostic BDD',
            'dbConnected' => $dbConnected,
            'existingTables' => $existingTables,
            'existingColumns' => $existingColumns,
            'pageResults' => $pageResults,
            'categories' => $categories,
            'totalOk' => $totalOk,
            'totalWarning' => $totalWarning,
            'totalError' => $totalError,
            'totalPages' => count($pageResults),
        ]);
    }

    /**
     * Retourne le schéma attendu (tables + colonnes) pour chaque page de l'application.
     */
    private static function getExpectedPageSchemas(): array
    {
        $leadsColumns = [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PK',
            'website_id' => 'INT UNSIGNED NOT NULL',
            'lead_type' => "ENUM('tendance','qualifie')",
            'nom' => 'VARCHAR(120)',
            'email' => 'VARCHAR(180)',
            'telephone' => 'VARCHAR(40)',
            'adresse' => 'VARCHAR(255)',
            'ville' => 'VARCHAR(120) NOT NULL',
            'type_bien' => 'VARCHAR(80)',
            'surface_m2' => 'DECIMAL(8,2)',
            'pieces' => 'INT UNSIGNED',
            'estimation' => 'DECIMAL(12,2) NOT NULL',
            'urgence' => 'VARCHAR(40)',
            'motivation' => 'VARCHAR(80)',
            'notes' => 'TEXT',
            'partenaire_id' => 'INT UNSIGNED',
            'commission_taux' => 'DECIMAL(5,2)',
            'commission_montant' => 'DECIMAL(12,2)',
            'assigne_a' => 'VARCHAR(180)',
            'date_mandat' => 'DATE',
            'date_compromis' => 'DATE',
            'date_signature' => 'DATE',
            'prix_vente' => 'DECIMAL(12,2)',
            'score' => "ENUM('chaud','tiede','froid')",
            'statut' => "ENUM('nouveau','contacte','rdv_pris',...)",
            'created_at' => 'DATETIME NOT NULL',
        ];

        $leadNotesColumns = [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PK',
            'lead_id' => 'INT UNSIGNED NOT NULL (FK leads)',
            'content' => 'TEXT NOT NULL',
            'author' => 'VARCHAR(120)',
            'created_at' => 'DATETIME NOT NULL',
        ];

        $leadActivitiesColumns = [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PK',
            'lead_id' => 'INT UNSIGNED NOT NULL (FK leads)',
            'activity_type' => 'VARCHAR(50) NOT NULL',
            'description' => 'TEXT NOT NULL',
            'created_at' => 'DATETIME NOT NULL',
        ];

        $articlesColumns = [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PK',
            'website_id' => 'INT UNSIGNED NOT NULL',
            'title' => 'VARCHAR(255) NOT NULL',
            'slug' => 'VARCHAR(255) NOT NULL',
            'content' => 'LONGTEXT NOT NULL',
            'meta_title' => 'VARCHAR(255) NOT NULL',
            'meta_description' => 'TEXT NOT NULL',
            'persona' => 'VARCHAR(100) NOT NULL',
            'awareness_level' => 'VARCHAR(50) NOT NULL',
            'status' => "ENUM('draft','published')",
            'created_at' => 'DATETIME NOT NULL',
        ];

        $articleRevisionsColumns = [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PK',
            'article_id' => 'INT UNSIGNED NOT NULL (FK articles)',
            'revision_number' => 'INT UNSIGNED NOT NULL',
            'title' => 'VARCHAR(255) NOT NULL',
            'slug' => 'VARCHAR(255) NOT NULL',
            'content' => 'LONGTEXT NOT NULL',
            'meta_title' => 'VARCHAR(255) NOT NULL',
            'meta_description' => 'TEXT NOT NULL',
            'persona' => 'VARCHAR(100) NOT NULL',
            'awareness_level' => 'VARCHAR(50) NOT NULL',
            'status' => "ENUM('draft','published')",
            'created_at' => 'DATETIME NOT NULL',
        ];

        $actualitesColumns = [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PK',
            'website_id' => 'INT UNSIGNED NOT NULL',
            'title' => 'VARCHAR(255) NOT NULL',
            'slug' => 'VARCHAR(255) NOT NULL',
            'content' => 'LONGTEXT NOT NULL',
            'excerpt' => 'TEXT NOT NULL',
            'meta_title' => 'VARCHAR(255)',
            'meta_description' => 'TEXT',
            'image_url' => 'VARCHAR(500)',
            'image_prompt' => 'TEXT',
            'source_query' => 'TEXT',
            'source_results' => 'LONGTEXT',
            'status' => "ENUM('draft','published')",
            'generated_by' => "ENUM('manual','ai','cron')",
            'published_at' => 'DATETIME',
            'created_at' => 'DATETIME NOT NULL',
            'updated_at' => 'DATETIME NOT NULL',
        ];

        $cronLogColumns = [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PK',
            'website_id' => 'INT UNSIGNED NOT NULL',
            'query_used' => 'TEXT NOT NULL',
            'articles_found' => 'INT UNSIGNED',
            'article_published_id' => 'INT UNSIGNED',
            'status' => "ENUM('success','error')",
            'error_message' => 'TEXT',
            'created_at' => 'DATETIME NOT NULL',
        ];

        $partenairesColumns = [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PK',
            'website_id' => 'INT UNSIGNED NOT NULL',
            'nom' => 'VARCHAR(180) NOT NULL',
            'entreprise' => 'VARCHAR(255)',
            'email' => 'VARCHAR(180) NOT NULL',
            'telephone' => 'VARCHAR(40)',
            'specialite' => 'VARCHAR(120)',
            'zone_geographique' => 'VARCHAR(255)',
            'commission_defaut' => 'DECIMAL(5,2)',
            'statut' => "ENUM('actif','inactif','prospect')",
            'notes' => 'TEXT',
            'nb_mandats' => 'INT UNSIGNED',
            'ca_genere' => 'DECIMAL(12,2)',
            'created_at' => 'DATETIME NOT NULL',
            'updated_at' => 'DATETIME NOT NULL',
        ];

        $achatsColumns = [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PK',
            'website_id' => 'INT UNSIGNED NOT NULL',
            'lead_id' => 'INT UNSIGNED (FK leads)',
            'nom_acheteur' => 'VARCHAR(180) NOT NULL',
            'email_acheteur' => 'VARCHAR(180)',
            'telephone_acheteur' => 'VARCHAR(40)',
            'adresse_bien' => 'VARCHAR(255)',
            'ville' => 'VARCHAR(120) NOT NULL',
            'quartier' => 'VARCHAR(120)',
            'type_bien' => 'VARCHAR(80)',
            'surface_m2' => 'DECIMAL(8,2)',
            'pieces' => 'INT UNSIGNED',
            'prix_achat' => 'DECIMAL(12,2)',
            'prix_estime' => 'DECIMAL(12,2)',
            'type_financement' => "ENUM('comptant','credit','mixte')",
            'montant_pret' => 'DECIMAL(12,2)',
            'apport_personnel' => 'DECIMAL(12,2)',
            'statut' => "ENUM('prospect','recherche',...,'annule')",
            'score' => "ENUM('chaud','tiede','froid')",
            'partenaire_id' => 'INT UNSIGNED',
            'commission_taux' => 'DECIMAL(5,2)',
            'commission_montant' => 'DECIMAL(12,2)',
            'date_premiere_visite' => 'DATE',
            'date_offre' => 'DATE',
            'date_compromis' => 'DATE',
            'date_acte' => 'DATE',
            'notes' => 'TEXT',
            'created_at' => 'DATETIME NOT NULL',
            'updated_at' => 'DATETIME NOT NULL',
        ];

        $adminUsersColumns = [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PK',
            'email' => 'VARCHAR(180) NOT NULL UNIQUE',
            'name' => 'VARCHAR(120)',
            'login_code' => 'VARCHAR(255)',
            'login_code_expires_at' => 'DATETIME',
            'created_at' => 'DATETIME NOT NULL',
            'updated_at' => 'DATETIME NOT NULL',
        ];

        $newsletterColumns = [
            'id' => 'INT UNSIGNED AUTO_INCREMENT PK',
            'email' => 'VARCHAR(180) NOT NULL UNIQUE',
            'confirmed_at' => 'DATETIME NOT NULL',
            'created_at' => 'DATETIME NOT NULL',
        ];

        return [
            // ===== PAGES PUBLIQUES =====
            'home' => [
                'label' => 'Accueil',
                'route' => '/',
                'icon' => 'fa-home',
                'category' => 'Pages publiques',
                'tables' => [],
            ],
            'services' => [
                'label' => 'Services',
                'route' => '/services',
                'icon' => 'fa-concierge-bell',
                'category' => 'Pages publiques',
                'tables' => [],
            ],
            'a_propos' => [
                'label' => '&Agrave; propos',
                'route' => '/a-propos',
                'icon' => 'fa-info-circle',
                'category' => 'Pages publiques',
                'tables' => [],
            ],
            'processus_estimation' => [
                'label' => 'Processus d\'estimation',
                'route' => '/processus-estimation',
                'icon' => 'fa-list-ol',
                'category' => 'Pages publiques',
                'tables' => [],
            ],
            'quartiers' => [
                'label' => 'Quartiers',
                'route' => '/quartiers',
                'icon' => 'fa-map-marker-alt',
                'category' => 'Pages publiques',
                'tables' => [],
            ],
            'contact' => [
                'label' => 'Contact',
                'route' => '/contact',
                'icon' => 'fa-envelope',
                'category' => 'Pages publiques',
                'tables' => [],
            ],
            'guides' => [
                'label' => 'Guides',
                'route' => '/guides',
                'icon' => 'fa-book',
                'category' => 'Pages publiques',
                'tables' => [],
            ],
            'exemples_estimation' => [
                'label' => 'Exemples d\'estimation',
                'route' => '/exemples-estimation',
                'icon' => 'fa-chart-bar',
                'category' => 'Pages publiques',
                'tables' => [],
            ],
            'mentions_legales' => [
                'label' => 'Mentions l&eacute;gales',
                'route' => '/mentions-legales',
                'icon' => 'fa-gavel',
                'category' => 'Pages publiques',
                'tables' => [],
            ],
            'politique_confidentialite' => [
                'label' => 'Politique confidentialit&eacute;',
                'route' => '/politique-confidentialite',
                'icon' => 'fa-shield-alt',
                'category' => 'Pages publiques',
                'tables' => [],
            ],
            'conditions_utilisation' => [
                'label' => 'Conditions d\'utilisation',
                'route' => '/conditions-utilisation',
                'icon' => 'fa-file-contract',
                'category' => 'Pages publiques',
                'tables' => [],
            ],
            'rgpd' => [
                'label' => 'RGPD',
                'route' => '/rgpd',
                'icon' => 'fa-user-shield',
                'category' => 'Pages publiques',
                'tables' => [],
            ],

            // ===== ESTIMATION & LEADS =====
            'estimation' => [
                'label' => 'Estimation (formulaire + r&eacute;sultat)',
                'route' => '/estimation',
                'icon' => 'fa-calculator',
                'category' => 'Estimation & Leads',
                'tables' => [
                    'leads' => [
                        'description' => 'Stocke les leads tendance cr&eacute;&eacute;s lors d\'une estimation',
                        'columns' => $leadsColumns,
                    ],
                ],
            ],
            'lead_capture' => [
                'label' => 'Capture lead qualifi&eacute;',
                'route' => 'POST /lead',
                'icon' => 'fa-user-plus',
                'category' => 'Estimation & Leads',
                'tables' => [
                    'leads' => [
                        'description' => 'Cr&eacute;ation du lead qualifi&eacute; avec scoring',
                        'columns' => $leadsColumns,
                    ],
                ],
            ],
            'leads_list' => [
                'label' => 'Liste des leads (public)',
                'route' => '/leads',
                'icon' => 'fa-list',
                'category' => 'Estimation & Leads',
                'tables' => [
                    'leads' => [
                        'description' => 'Lecture des leads avec filtres',
                        'columns' => $leadsColumns,
                    ],
                ],
            ],

            // ===== NEWSLETTER =====
            'newsletter' => [
                'label' => 'Newsletter',
                'route' => '/newsletter',
                'icon' => 'fa-newspaper',
                'category' => 'Newsletter',
                'tables' => [
                    'newsletter_subscribers' => [
                        'description' => 'Inscription et confirmation des abonn&eacute;s',
                        'columns' => $newsletterColumns,
                    ],
                ],
            ],

            // ===== BLOG =====
            'blog_index' => [
                'label' => 'Blog (liste)',
                'route' => '/blog',
                'icon' => 'fa-blog',
                'category' => 'Blog',
                'tables' => [
                    'articles' => [
                        'description' => 'Articles publi&eacute;s',
                        'columns' => $articlesColumns,
                    ],
                ],
            ],
            'blog_show' => [
                'label' => 'Blog (article)',
                'route' => '/blog/{slug}',
                'icon' => 'fa-file-alt',
                'category' => 'Blog',
                'tables' => [
                    'articles' => [
                        'description' => 'D&eacute;tail d\'un article par slug',
                        'columns' => $articlesColumns,
                    ],
                ],
            ],
            'admin_blog' => [
                'label' => 'Admin Blog',
                'route' => '/admin/blog',
                'icon' => 'fa-pen-fancy',
                'category' => 'Blog',
                'tables' => [
                    'articles' => [
                        'description' => 'CRUD articles (brouillons + publi&eacute;s)',
                        'columns' => $articlesColumns,
                    ],
                    'article_revisions' => [
                        'description' => 'Historique des r&eacute;visions',
                        'columns' => $articleRevisionsColumns,
                    ],
                ],
            ],

            // ===== ACTUALITES =====
            'actualites_index' => [
                'label' => 'Actualit&eacute;s (liste)',
                'route' => '/actualites',
                'icon' => 'fa-rss',
                'category' => 'Actualit&eacute;s',
                'tables' => [
                    'actualites' => [
                        'description' => 'Actualit&eacute;s publi&eacute;es (pagin&eacute;es)',
                        'columns' => $actualitesColumns,
                    ],
                ],
            ],
            'actualites_show' => [
                'label' => 'Actualit&eacute;s (d&eacute;tail)',
                'route' => '/actualites/{slug}',
                'icon' => 'fa-file-alt',
                'category' => 'Actualit&eacute;s',
                'tables' => [
                    'actualites' => [
                        'description' => 'D&eacute;tail d\'une actualit&eacute; par slug',
                        'columns' => $actualitesColumns,
                    ],
                ],
            ],
            'admin_actualites' => [
                'label' => 'Admin Actualit&eacute;s',
                'route' => '/admin/actualites',
                'icon' => 'fa-newspaper',
                'category' => 'Actualit&eacute;s',
                'tables' => [
                    'actualites' => [
                        'description' => 'CRUD actualit&eacute;s + g&eacute;n&eacute;ration IA',
                        'columns' => $actualitesColumns,
                    ],
                    'actualites_cron_log' => [
                        'description' => 'Logs de g&eacute;n&eacute;ration automatique',
                        'columns' => $cronLogColumns,
                    ],
                ],
            ],

            // ===== ADMIN CRM =====
            'admin_leads' => [
                'label' => 'Admin Leads (CRM)',
                'route' => '/admin/leads',
                'icon' => 'fa-users',
                'category' => 'Admin CRM',
                'tables' => [
                    'leads' => [
                        'description' => 'Gestion compl&egrave;te des leads',
                        'columns' => $leadsColumns,
                    ],
                    'lead_notes' => [
                        'description' => 'Notes CRM sur chaque lead',
                        'columns' => $leadNotesColumns,
                    ],
                    'lead_activities' => [
                        'description' => 'Journal d\'activit&eacute;s (changements de statut, etc.)',
                        'columns' => $leadActivitiesColumns,
                    ],
                    'partenaires' => [
                        'description' => 'R&eacute;f&eacute;rence partenaires (assignation)',
                        'columns' => $partenairesColumns,
                    ],
                ],
            ],
            'admin_dashboard' => [
                'label' => 'Dashboard Admin',
                'route' => '/admin',
                'icon' => 'fa-tachometer-alt',
                'category' => 'Admin CRM',
                'tables' => [
                    'leads' => [
                        'description' => 'Statistiques leads (COUNT, SUM estimation, SUM commission)',
                        'columns' => $leadsColumns,
                    ],
                    'articles' => [
                        'description' => 'Comptage articles',
                        'columns' => $articlesColumns,
                    ],
                ],
            ],
            'admin_funnel' => [
                'label' => 'Funnel de conversion',
                'route' => '/admin/funnel',
                'icon' => 'fa-filter',
                'category' => 'Admin CRM',
                'tables' => [
                    'leads' => [
                        'description' => 'Analyse du tunnel de conversion par statut',
                        'columns' => $leadsColumns,
                    ],
                ],
            ],
            'admin_portfolio' => [
                'label' => 'Portfolio',
                'route' => '/admin/portfolio',
                'icon' => 'fa-briefcase',
                'category' => 'Admin CRM',
                'tables' => [
                    'leads' => [
                        'description' => 'Analyse portfolio et commissions',
                        'columns' => $leadsColumns,
                    ],
                ],
            ],

            // ===== ACHATS =====
            'admin_achats' => [
                'label' => 'Admin Achats',
                'route' => '/admin/achats',
                'icon' => 'fa-shopping-cart',
                'category' => 'Achats',
                'tables' => [
                    'achats' => [
                        'description' => 'Gestion des achats immobiliers',
                        'columns' => $achatsColumns,
                    ],
                    'partenaires' => [
                        'description' => 'R&eacute;f&eacute;rence partenaires (assignation)',
                        'columns' => $partenairesColumns,
                    ],
                ],
            ],

            // ===== PARTENAIRES =====
            'admin_partenaires' => [
                'label' => 'Admin Partenaires',
                'route' => '/admin/partenaires',
                'icon' => 'fa-handshake',
                'category' => 'Partenaires',
                'tables' => [
                    'partenaires' => [
                        'description' => 'CRUD partenaires + statistiques',
                        'columns' => $partenairesColumns,
                    ],
                ],
            ],

            // ===== AUTHENTIFICATION =====
            'admin_login' => [
                'label' => 'Connexion Admin',
                'route' => '/admin/login',
                'icon' => 'fa-sign-in-alt',
                'category' => 'Authentification',
                'tables' => [
                    'admin_users' => [
                        'description' => 'V&eacute;rification email + code de connexion',
                        'columns' => $adminUsersColumns,
                    ],
                ],
            ],

            // ===== LANDING PAGES =====
            'lp_estimation' => [
                'label' => 'LP Estimation Nandy',
                'route' => '/lp/estimation-nandy',
                'icon' => 'fa-bullseye',
                'category' => 'Landing Pages',
                'tables' => [
                    'leads' => [
                        'description' => 'Capture lead via landing page',
                        'columns' => $leadsColumns,
                    ],
                ],
            ],
            'lp_vendre' => [
                'label' => 'LP Vendre Maison',
                'route' => '/lp/vendre-maison-nandy',
                'icon' => 'fa-bullseye',
                'category' => 'Landing Pages',
                'tables' => [
                    'leads' => [
                        'description' => 'Capture lead via landing page',
                        'columns' => $leadsColumns,
                    ],
                ],
            ],
            'lp_avis' => [
                'label' => 'LP Avis de Valeur',
                'route' => '/lp/avis-valeur-gratuit',
                'icon' => 'fa-bullseye',
                'category' => 'Landing Pages',
                'tables' => [
                    'leads' => [
                        'description' => 'Capture lead via landing page',
                        'columns' => $leadsColumns,
                    ],
                ],
            ],

            // ===== OUTILS ADMIN (sans BDD) =====
            'admin_emails' => [
                'label' => 'Admin Emails',
                'route' => '/admin/emails',
                'icon' => 'fa-envelope-open-text',
                'category' => 'Outils Admin',
                'tables' => [],
            ],
            'admin_sequences' => [
                'label' => 'Admin S&eacute;quences',
                'route' => '/admin/sequences',
                'icon' => 'fa-stream',
                'category' => 'Outils Admin',
                'tables' => [
                    'articles' => [
                        'description' => 'Suggestions d\'articles pour les s&eacute;quences',
                        'columns' => $articlesColumns,
                    ],
                ],
            ],
            'admin_images' => [
                'label' => 'Admin Images IA',
                'route' => '/admin/images',
                'icon' => 'fa-images',
                'category' => 'Outils Admin',
                'tables' => [],
            ],
            'admin_social_images' => [
                'label' => 'Admin Images Sociales',
                'route' => '/admin/social-images',
                'icon' => 'fa-share-alt',
                'category' => 'Outils Admin',
                'tables' => [],
            ],
            'admin_database' => [
                'label' => 'Admin Base de donn&eacute;es',
                'route' => '/admin/database',
                'icon' => 'fa-database',
                'category' => 'Outils Admin',
                'tables' => [],
            ],
            'admin_api' => [
                'label' => 'Admin API',
                'route' => '/admin/api-management',
                'icon' => 'fa-key',
                'category' => 'Outils Admin',
                'tables' => [],
            ],
            'admin_google_ads' => [
                'label' => 'Guide Google Ads',
                'route' => '/admin/google-ads',
                'icon' => 'fa-ad',
                'category' => 'Outils Admin',
                'tables' => [],
            ],
            'tools_calculatrice' => [
                'label' => 'Calculatrice',
                'route' => '/tools/calculatrice',
                'icon' => 'fa-calculator',
                'category' => 'Outils Admin',
                'tables' => [],
            ],
        ];
    }
}
