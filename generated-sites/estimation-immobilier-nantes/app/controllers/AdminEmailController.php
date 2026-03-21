<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\View;
use App\Services\Mailer;

final class AdminEmailController
{
    public function index(): void
    {
        AuthController::requireAuth();

        $pdo = Database::connection();
        $templates = [];
        $sentEmails = [];

        try {
            if (Database::tableExists('email_templates')) {
                $stmt = $pdo->query('SELECT * FROM email_templates ORDER BY updated_at DESC');
                $templates = $stmt->fetchAll();
            }

            if (Database::tableExists('email_logs')) {
                $stmt = $pdo->query('SELECT * FROM email_logs ORDER BY sent_at DESC LIMIT 50');
                $sentEmails = $stmt->fetchAll();
            }
        } catch (\Throwable $e) {
            error_log('AdminEmail error: ' . $e->getMessage());
        }

        View::renderAdmin('admin/emails', [
            'page_title' => 'Gestion des Emails',
            'admin_page_title' => 'Templates Email',
            'admin_page' => 'emails',
            'breadcrumb' => 'Emails',
            'templates' => $templates,
            'sentEmails' => $sentEmails,
        ]);
    }

    public function edit(): void
    {
        AuthController::requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        $template = null;

        if ($id > 0) {
            $pdo = Database::connection();
            $stmt = $pdo->prepare('SELECT * FROM email_templates WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $template = $stmt->fetch();
        }

        View::renderAdmin('admin/email-edit', [
            'page_title' => $template ? 'Modifier le template' : 'Nouveau template',
            'admin_page' => 'emails',
            'breadcrumb' => $template ? 'Modifier email' : 'Nouveau email',
            'template' => $template,
        ]);
    }

    public function save(): void
    {
        AuthController::requireAuth();

        $id = (int) ($_POST['id'] ?? 0);
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $name = trim((string) ($_POST['name'] ?? ''));
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $bodyHtml = trim((string) ($_POST['body_html'] ?? ''));
        $signature = trim((string) ($_POST['signature'] ?? ''));
        $category = trim((string) ($_POST['category'] ?? 'notification'));

        if ($name === '' || $subject === '') {
            $_SESSION['email_flash'] = ['type' => 'error', 'message' => 'Nom et sujet sont requis.'];
            header('Location: /admin/emails/edit' . ($id > 0 ? '?id=' . $id : ''));
            exit;
        }

        if ($slug === '') {
            $slug = $this->slugify($name);
        }

        $pdo = Database::connection();

        try {
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE email_templates SET slug = :slug, name = :name, subject = :subject, body_html = :body_html, signature = :signature, category = :category, updated_at = NOW() WHERE id = :id');
                $stmt->execute([
                    'slug' => $slug,
                    'name' => $name,
                    'subject' => $subject,
                    'body_html' => $bodyHtml,
                    'signature' => $signature,
                    'category' => $category,
                    'id' => $id,
                ]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO email_templates (slug, name, subject, body_html, signature, category, created_at, updated_at) VALUES (:slug, :name, :subject, :body_html, :signature, :category, NOW(), NOW())');
                $stmt->execute([
                    'slug' => $slug,
                    'name' => $name,
                    'subject' => $subject,
                    'body_html' => $bodyHtml,
                    'signature' => $signature,
                    'category' => $category,
                ]);
            }

            $_SESSION['email_flash'] = ['type' => 'success', 'message' => 'Template sauvegard&eacute; avec succ&egrave;s.'];
        } catch (\Throwable $e) {
            $_SESSION['email_flash'] = ['type' => 'error', 'message' => 'Erreur: ' . $e->getMessage()];
        }

        header('Location: /admin/emails');
        exit;
    }

    public function delete(): void
    {
        AuthController::requireAuth();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo = Database::connection();
            $stmt = $pdo->prepare('DELETE FROM email_templates WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $_SESSION['email_flash'] = ['type' => 'success', 'message' => 'Template supprim&eacute;.'];
        }

        header('Location: /admin/emails');
        exit;
    }

    public function sendTest(): void
    {
        AuthController::requireAuth();

        $to = trim((string) ($_POST['to'] ?? ''));
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $body = trim((string) ($_POST['body'] ?? ''));
        $signature = trim((string) ($_POST['signature'] ?? ''));

        if ($to === '' || $subject === '' || $body === '') {
            echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis.']);
            return;
        }

        $fullBody = $body;
        if ($signature !== '') {
            $fullBody .= '<br><br>--<br>' . nl2br(htmlspecialchars($signature, ENT_QUOTES, 'UTF-8'));
        }

        $sent = Mailer::send($to, $subject, $fullBody);

        // Log the email
        try {
            $pdo = Database::connection();
            if (Database::tableExists('email_logs')) {
                $stmt = $pdo->prepare('INSERT INTO email_logs (recipient, subject, body_html, status, sent_at) VALUES (:recipient, :subject, :body, :status, NOW())');
                $stmt->execute([
                    'recipient' => $to,
                    'subject' => $subject,
                    'body' => $fullBody,
                    'status' => $sent ? 'sent' : 'failed',
                ]);
            }
        } catch (\Throwable $e) {
            error_log('Email log error: ' . $e->getMessage());
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $sent,
            'message' => $sent ? 'Email envoy&eacute; avec succ&egrave;s.' : '&Eacute;chec de l\'envoi.',
        ]);
    }

    /**
     * AI-assisted content generation for email fields.
     */
    public function aiGenerate(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        $field = trim((string) ($_POST['field'] ?? ''));
        $context = trim((string) ($_POST['context'] ?? ''));
        $currentValue = trim((string) ($_POST['current_value'] ?? ''));

        $apiKey = (string) Config::get('openai.api_key');
        if ($apiKey === '') {
            echo json_encode(['success' => false, 'message' => 'Cl&eacute; API OpenAI non configur&eacute;e.']);
            return;
        }

        $prompts = [
            'subject' => "G&eacute;n&egrave;re un objet d'email professionnel et engageant pour un email immobilier. Contexte: {$context}. Valeur actuelle: {$currentValue}. R&eacute;ponds uniquement avec l'objet, sans guillemets.",
            'body' => "G&eacute;n&egrave;re le contenu HTML d'un email professionnel immobilier. Contexte: {$context}. Contenu actuel: {$currentValue}. Utilise un ton professionnel mais chaleureux. Inclus des balises HTML basiques (<p>, <strong>, <br>). Ne g&eacute;n&egrave;re pas la signature.",
            'signature' => "G&eacute;n&egrave;re une signature email professionnelle pour un agent immobilier &agrave; Nantes. Contexte: {$context}. Signature actuelle: {$currentValue}. Format texte simple avec retours &agrave; la ligne.",
        ];

        $prompt = $prompts[$field] ?? "Am&eacute;liore ce texte pour un email immobilier professionnel: {$currentValue}";

        try {
            $endpoint = (string) Config::get('openai.endpoint', 'https://api.openai.com/v1/chat/completions');
            $model = (string) Config::get('openai.model', 'gpt-4o-mini');

            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey,
                ],
                CURLOPT_POSTFIELDS => json_encode([
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Tu es un assistant sp&eacute;cialis&eacute; en r&eacute;daction d\'emails immobiliers professionnels pour le march&eacute; de nantes.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                ]),
                CURLOPT_TIMEOUT => 30,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || $response === false) {
                echo json_encode(['success' => false, 'message' => 'Erreur API (HTTP ' . $httpCode . ')']);
                return;
            }

            $data = json_decode($response, true);
            $content = $data['choices'][0]['message']['content'] ?? '';

            echo json_encode(['success' => true, 'content' => $content]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    private function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        return trim($text, '-');
    }
}
