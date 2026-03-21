<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class AdminSocialImageController
{
    public function index(): void
    {
        $images = $this->listSavedImages();

        View::renderAdmin('admin/social-images/index', [
            'page_title' => 'Images Réseaux Sociaux',
            'admin_page_title' => 'Images Sociales',
            'admin_page' => 'social-images',
            'breadcrumb' => 'Images Sociales',
            'images' => $images,
            'quartiers' => QUARTIERS,
            'prix_m2' => PRIX_M2_MOYEN,
        ]);
    }

    public function history(): void
    {
        $images = $this->listSavedImages();

        View::renderAdmin('admin/social-images/history', [
            'page_title' => 'Historique des images',
            'admin_page' => 'social-images',
            'breadcrumb' => 'Historique',
            'images' => $images,
        ]);
    }

    public function save(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents('php://input') ?: '{}', true);
        if (!is_array($input) || empty($input['image_data'])) {
            echo json_encode(['success' => false, 'error' => 'Données image manquantes.']);
            return;
        }

        $imageData = $input['image_data'];
        $filename = $input['filename'] ?? ('social-' . date('Y-m-d-His') . '.png');

        // Validate and sanitize filename
        $filename = preg_replace('/[^a-zA-Z0-9_\-.]/', '', $filename);
        if ($filename === '' || $filename === '.png') {
            $filename = 'social-' . date('Y-m-d-His') . '.png';
        }
        if (!str_ends_with($filename, '.png')) {
            $filename .= '.png';
        }

        $dir = __DIR__ . '/../../public/assets/images/social/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Decode base64 image data
        $data = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
        if ($data === null || $data === '') {
            echo json_encode(['success' => false, 'error' => 'Données image invalides.']);
            return;
        }

        $decoded = base64_decode($data, true);
        if ($decoded === false) {
            echo json_encode(['success' => false, 'error' => 'Décodage base64 échoué.']);
            return;
        }

        $filepath = $dir . $filename;
        if (file_put_contents($filepath, $decoded) === false) {
            echo json_encode(['success' => false, 'error' => 'Impossible de sauvegarder l\'image.']);
            return;
        }

        echo json_encode([
            'success' => true,
            'filename' => $filename,
            'url' => '/assets/images/social/' . $filename,
            'size' => strlen($decoded),
        ], JSON_UNESCAPED_UNICODE);
    }

    public function delete(): void
    {
        $filename = trim((string) ($_POST['filename'] ?? ''));
        if ($filename === '') {
            $this->redirect('/admin/social-images?error=' . urlencode('Nom de fichier manquant.'));
            return;
        }

        $filepath = __DIR__ . '/../../public/assets/images/social/' . basename($filename);
        if (is_file($filepath) && unlink($filepath)) {
            $this->redirect('/admin/social-images?message=' . urlencode('Image supprimée.'));
        } else {
            $this->redirect('/admin/social-images?error=' . urlencode('Impossible de supprimer l\'image.'));
        }
    }

    private function listSavedImages(): array
    {
        $dir = __DIR__ . '/../../public/assets/images/social/';
        if (!is_dir($dir)) {
            return [];
        }

        $images = [];
        foreach (glob($dir . '*.png') as $file) {
            $images[] = [
                'filename' => basename($file),
                'url' => '/assets/images/social/' . basename($file),
                'size' => filesize($file),
                'created_at' => date('Y-m-d H:i', filemtime($file)),
            ];
        }

        usort($images, fn(array $a, array $b) => strcmp($b['created_at'], $a['created_at']));

        return $images;
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
