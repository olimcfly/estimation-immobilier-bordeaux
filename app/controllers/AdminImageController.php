<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ImageGeneratorService;

final class AdminImageController
{
    public function index(): void
    {
        $service = new ImageGeneratorService();

        View::render('admin/images/index', [
            'images' => $service->listGeneratedImages(),
            'promptTypes' => $this->promptTypes(),
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => (string) ($_GET['error'] ?? ''),
        ]);
    }

    public function generate(): void
    {
        $promptMode = trim((string) ($_POST['prompt_mode'] ?? 'custom'));
        $service = new ImageGeneratorService();

        if ($promptMode === 'seo') {
            $type = trim((string) ($_POST['seo_type'] ?? 'blog'));
            $quartier = trim((string) ($_POST['quartier'] ?? ''));
            $style = trim((string) ($_POST['style'] ?? ''));
            $prompt = $service->generateSeoPrompt($type, $quartier, $style);
        } else {
            $prompt = trim((string) ($_POST['prompt'] ?? ''));
        }

        if ($prompt === '') {
            $this->redirect('/admin/images?error=' . urlencode('Le prompt est requis.'));
            return;
        }

        $size = trim((string) ($_POST['size'] ?? '1024x1024'));
        $quality = trim((string) ($_POST['quality'] ?? 'medium'));

        $result = $service->generate($prompt, $size, $quality);

        if (!$result['success']) {
            View::render('admin/images/index', [
                'images' => $service->listGeneratedImages(),
                'promptTypes' => $this->promptTypes(),
                'error' => $result['error'],
                'message' => '',
                'lastPrompt' => $prompt,
                'lastSize' => $size,
                'lastQuality' => $quality,
            ]);
            return;
        }

        View::render('admin/images/index', [
            'images' => $service->listGeneratedImages(),
            'promptTypes' => $this->promptTypes(),
            'message' => 'Image générée avec succès !',
            'error' => '',
            'generated' => $result,
            'lastPrompt' => $prompt,
        ]);
    }

    public function delete(): void
    {
        $filename = trim((string) ($_POST['filename'] ?? ''));

        if ($filename === '') {
            $this->redirect('/admin/images?error=' . urlencode('Nom de fichier manquant.'));
            return;
        }

        $service = new ImageGeneratorService();
        if ($service->deleteImage($filename)) {
            $this->redirect('/admin/images?message=' . urlencode('Image supprimée.'));
        } else {
            $this->redirect('/admin/images?error=' . urlencode('Impossible de supprimer l\'image.'));
        }
    }

    public function apiGenerate(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents('php://input') ?: '{}', true);
        if (!is_array($input)) {
            echo json_encode(['success' => false, 'error' => 'JSON invalide.']);
            return;
        }

        $prompt = trim((string) ($input['prompt'] ?? ''));
        if ($prompt === '') {
            echo json_encode(['success' => false, 'error' => 'Le prompt est requis.']);
            return;
        }

        $size = trim((string) ($input['size'] ?? '1024x1024'));
        $quality = trim((string) ($input['quality'] ?? 'medium'));

        $service = new ImageGeneratorService();
        $result = $service->generate($prompt, $size, $quality);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function apiSeoPrompt(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $type = trim((string) ($_GET['type'] ?? 'blog'));
        $quartier = trim((string) ($_GET['quartier'] ?? ''));
        $style = trim((string) ($_GET['style'] ?? ''));

        $service = new ImageGeneratorService();
        $prompt = $service->generateSeoPrompt($type, $quartier, $style);

        echo json_encode(['prompt' => $prompt], JSON_UNESCAPED_UNICODE);
    }

    private function promptTypes(): array
    {
        return [
            'estimation' => 'Estimation immobilière',
            'interieur' => 'Intérieur appartement',
            'quartier' => 'Vue quartier',
            'blog' => 'Illustration blog',
            'cta' => 'Call-to-action',
        ];
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
