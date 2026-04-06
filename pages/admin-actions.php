<?php
require_once __DIR__ . '/../config/database.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'db_backup':
        $db = Database::getConnection();
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="backup-' . date('Y-m-d-His') . '.sql"');

        $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $rows = $db->query('SELECT * FROM `' . str_replace('`', '``', $table) . '`')->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $values = array_map(function ($v) use ($db) {
                    if ($v === null) {
                        return 'NULL';
                    }
                    return $db->quote($v);
                }, $row);
                echo 'INSERT INTO `' . $table . '` VALUES (' . implode(',', $values) . ");\n";
            }
            echo "\n";
        }
        exit;

    default:
        header('Location: dashboard.php');
        exit;
}
