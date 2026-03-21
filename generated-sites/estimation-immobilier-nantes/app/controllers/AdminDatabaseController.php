<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\View;
use PDO;
use PDOException;

final class AdminDatabaseController
{
    public function index(): void
    {
        AuthController::requireAuth();

        $connected = false;
        $error = '';
        $tables = [];
        $tableDetails = [];
        $missingItems = [];

        // Use custom credentials if provided, otherwise use config
        $dbHost = trim((string) ($_POST['db_host'] ?? $_SESSION['db_admin_host'] ?? Config::get('db.host', '127.0.0.1')));
        $dbPort = (int) ($_POST['db_port'] ?? $_SESSION['db_admin_port'] ?? Config::get('db.port', 3306));
        $dbName = trim((string) ($_POST['db_name'] ?? $_SESSION['db_admin_name'] ?? Config::get('db.name', '')));
        $dbUser = trim((string) ($_POST['db_user'] ?? $_SESSION['db_admin_user'] ?? Config::get('db.user', 'root')));
        $dbPass = (string) ($_POST['db_pass'] ?? $_SESSION['db_admin_pass'] ?? Config::get('db.pass', ''));

        $action = (string) ($_POST['action'] ?? '');

        // Auto-connect on first visit using app config if not yet connected and no explicit action
        $autoConnect = ($action === '' && empty($_SESSION['db_admin_connected']) && $dbName !== '');

        if ($action === 'connect' || !empty($_SESSION['db_admin_connected']) || $autoConnect) {
            try {
                $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $dbHost, $dbPort, $dbName);
                $pdo = new PDO($dsn, $dbUser, $dbPass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 5,
                ]);

                $connected = true;

                // Store in session
                $_SESSION['db_admin_host'] = $dbHost;
                $_SESSION['db_admin_port'] = $dbPort;
                $_SESSION['db_admin_name'] = $dbName;
                $_SESSION['db_admin_user'] = $dbUser;
                $_SESSION['db_admin_pass'] = $dbPass;
                $_SESSION['db_admin_connected'] = true;

                // Get tables
                $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

                // Get table details
                foreach ($tables as $table) {
                    $columns = $pdo->query("SHOW FULL COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
                    $rowCount = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
                    $tableDetails[$table] = [
                        'columns' => $columns,
                        'row_count' => $rowCount,
                    ];
                }

                // Detect missing tables/columns from code
                $missingItems = $this->detectMissingItems($pdo, $tables, $tableDetails);

            } catch (PDOException $e) {
                $error = $autoConnect
                    ? 'Connexion automatique échouée: ' . $e->getMessage()
                    : $e->getMessage();
                $_SESSION['db_admin_connected'] = false;
            }
        }

        // Handle create table
        if ($action === 'create_table' && !empty($_SESSION['db_admin_connected'])) {
            $this->handleCreateTable();
            return;
        }

        // Handle create column
        if ($action === 'create_column' && !empty($_SESSION['db_admin_connected'])) {
            $this->handleCreateColumn();
            return;
        }

        // Handle disconnect
        if ($action === 'disconnect') {
            unset(
                $_SESSION['db_admin_host'],
                $_SESSION['db_admin_port'],
                $_SESSION['db_admin_name'],
                $_SESSION['db_admin_user'],
                $_SESSION['db_admin_pass'],
                $_SESSION['db_admin_connected']
            );
            header('Location: /admin/database');
            exit;
        }

        View::renderAdmin('admin/database', [
            'page_title' => 'Administration Base de Données',
            'admin_page_title' => 'Base de données',
            'admin_page' => 'database',
            'breadcrumb' => 'Base de Données',
            'connected' => $connected,
            'error' => $error,
            'tables' => $tables,
            'tableDetails' => $tableDetails,
            'missingItems' => $missingItems,
            'dbHost' => $dbHost,
            'dbPort' => $dbPort,
            'dbName' => $dbName,
            'dbUser' => $dbUser,
            'dbPass' => $dbPass,
        ]);
    }

    private function handleCreateTable(): void
    {
        $tableName = trim((string) ($_POST['table_name'] ?? ''));
        $columnsJson = trim((string) ($_POST['columns_json'] ?? ''));

        if ($tableName === '') {
            $_SESSION['db_flash'] = ['type' => 'error', 'message' => 'Nom de table requis.'];
            header('Location: /admin/database');
            exit;
        }

        // Validate table name
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
            $_SESSION['db_flash'] = ['type' => 'error', 'message' => 'Nom de table invalide.'];
            header('Location: /admin/database');
            exit;
        }

        try {
            $pdo = $this->getAdminConnection();

            $columns = [];
            if ($columnsJson !== '') {
                $columns = json_decode($columnsJson, true) ?: [];
            }

            if (empty($columns)) {
                $columns = [
                    ['name' => 'id', 'type' => 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY'],
                    ['name' => 'created_at', 'type' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'],
                ];
            }

            $colDefs = [];
            foreach ($columns as $col) {
                $colName = preg_replace('/[^a-zA-Z0-9_]/', '', $col['name'] ?? '');
                $colType = $col['type'] ?? 'VARCHAR(255)';
                if ($colName !== '') {
                    $colDefs[] = "`{$colName}` {$colType}";
                }
            }

            $sql = "CREATE TABLE IF NOT EXISTS `{$tableName}` (\n  " . implode(",\n  ", $colDefs) . "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $pdo->exec($sql);

            $_SESSION['db_flash'] = ['type' => 'success', 'message' => "Table '{$tableName}' créée avec succès."];
        } catch (PDOException $e) {
            $_SESSION['db_flash'] = ['type' => 'error', 'message' => 'Erreur: ' . $e->getMessage()];
        }

        header('Location: /admin/database');
        exit;
    }

    private function handleCreateColumn(): void
    {
        $tableName = trim((string) ($_POST['target_table'] ?? ''));
        $colName = trim((string) ($_POST['col_name'] ?? ''));
        $colType = trim((string) ($_POST['col_type'] ?? 'VARCHAR(255)'));
        $colDefault = trim((string) ($_POST['col_default'] ?? ''));
        $colNullable = !empty($_POST['col_nullable']);

        if ($tableName === '' || $colName === '') {
            $_SESSION['db_flash'] = ['type' => 'error', 'message' => 'Nom de table et de colonne requis.'];
            header('Location: /admin/database');
            exit;
        }

        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $colName)) {
            $_SESSION['db_flash'] = ['type' => 'error', 'message' => 'Nom de colonne invalide.'];
            header('Location: /admin/database');
            exit;
        }

        try {
            $pdo = $this->getAdminConnection();

            $nullable = $colNullable ? 'NULL' : 'NOT NULL';
            $default = '';
            if ($colDefault !== '') {
                $default = "DEFAULT '{$colDefault}'";
            } elseif ($colNullable) {
                $default = 'DEFAULT NULL';
            }

            $sql = "ALTER TABLE `{$tableName}` ADD COLUMN `{$colName}` {$colType} {$nullable} {$default}";
            $pdo->exec($sql);

            $_SESSION['db_flash'] = ['type' => 'success', 'message' => "Colonne '{$colName}' ajoutée à '{$tableName}'."];
        } catch (PDOException $e) {
            $_SESSION['db_flash'] = ['type' => 'error', 'message' => 'Erreur: ' . $e->getMessage()];
        }

        header('Location: /admin/database');
        exit;
    }

    private function getAdminConnection(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $_SESSION['db_admin_host'] ?? Config::get('db.host'),
            (int) ($_SESSION['db_admin_port'] ?? Config::get('db.port', 3306)),
            $_SESSION['db_admin_name'] ?? Config::get('db.name'),
        );

        return new PDO($dsn, $_SESSION['db_admin_user'] ?? '', $_SESSION['db_admin_pass'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    /**
     * Scan code files to detect table/column references that don't exist in the database.
     */
    private function detectMissingItems(PDO $pdo, array $existingTables, array $tableDetails): array
    {
        $missing = [];
        $appDir = dirname(__DIR__);

        // Scan models, controllers, and views for table/column references
        $patterns = [
            // SQL queries referencing tables: FROM table, INTO table, UPDATE table, JOIN table
            '/(?:FROM|INTO|UPDATE|JOIN|TABLE)\s+`?([a-zA-Z_][a-zA-Z0-9_]*)`?/i',
            // Column references in queries: table.column
            '/`?([a-zA-Z_][a-zA-Z0-9_]*)`?\s*\.\s*`?([a-zA-Z_][a-zA-Z0-9_]*)`?/',
            // Array key access that might be column names: $row['column_name']
            '/\$(?:row|lead|article|data|result|user)\[[\'"]([\w]+)[\'"]\]/',
        ];

        $files = $this->getPhpFiles($appDir);
        $referencedTables = [];
        $referencedColumns = [];

        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            // Find table references
            if (preg_match_all($patterns[0], $content, $matches)) {
                foreach ($matches[1] as $table) {
                    $table = strtolower($table);
                    if (!in_array($table, ['information_schema', 'dual', 'select', 'where', 'set'], true)) {
                        $referencedTables[$table] = $referencedTables[$table] ?? [];
                        $referencedTables[$table][] = basename($file);
                    }
                }
            }

            // Find table.column references
            if (preg_match_all($patterns[1], $content, $matches)) {
                for ($i = 0; $i < count($matches[0]); $i++) {
                    $table = strtolower($matches[1][$i]);
                    $column = strtolower($matches[2][$i]);
                    if (in_array($table, $existingTables, true)) {
                        $referencedColumns[$table][$column] = $referencedColumns[$table][$column] ?? [];
                        $referencedColumns[$table][$column][] = basename($file);
                    }
                }
            }
        }

        // Check missing tables
        foreach ($referencedTables as $table => $files) {
            if (!in_array($table, $existingTables, true)) {
                $missing[] = [
                    'type' => 'table',
                    'name' => $table,
                    'referenced_in' => array_unique($files),
                ];
            }
        }

        // Check missing columns
        foreach ($referencedColumns as $table => $columns) {
            $existingCols = array_map(
                fn($c) => strtolower($c['Field']),
                $tableDetails[$table]['columns'] ?? []
            );
            foreach ($columns as $column => $files) {
                if (!in_array($column, $existingCols, true)) {
                    $missing[] = [
                        'type' => 'column',
                        'table' => $table,
                        'name' => $column,
                        'referenced_in' => array_unique($files),
                    ];
                }
            }
        }

        return $missing;
    }

    private function getPhpFiles(string $dir): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }
}
