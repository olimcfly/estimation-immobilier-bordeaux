<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, array $action): void
    {
        $this->routes['GET'][] = ['path' => $path, 'action' => $action];
    }

    public function post(string $path, array $action): void
    {
        $this->routes['POST'][] = ['path' => $path, 'action' => $action];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        parse_str((string) parse_url($uri, PHP_URL_QUERY), $queryParams);

        if ($path === '/public' || $path === '/public/') {
            $path = '/';
        } elseif (str_starts_with($path, '/public/')) {
            $path = substr($path, strlen('/public'));
        }

        if ($path === '/index.php') {
            $path = '/';
        } elseif (str_starts_with($path, '/index.php/')) {
            $path = substr($path, strlen('/index.php'));
        }

        // Normalize trailing slash: /admin/ → /admin (except root /)
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        if ($path === '/front/page.php') {
            $legacyPage = $this->sanitizeLegacyPage($queryParams['page'] ?? null);
            if ($legacyPage !== null) {
                $path = '/' . $legacyPage;
            }
        }

        [$action, $params] = $this->resolveRoute($method, $path);

        if ($action === null) {
            $this->logNotFoundRoute($method, $uri, $path, $queryParams);
            $this->fallbackToHomeOr404($method);
            return;
        }

        [$controllerClass, $controllerMethod] = $action;
        $controller = new $controllerClass();

        try {
            $controller->{$controllerMethod}(...$params);
        } catch (\Throwable $e) {
            error_log(sprintf('[router][error] %s::%s - %s', $controllerClass, $controllerMethod, $e->getMessage()));
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
            echo '<h1>Erreur 500</h1><p>Une erreur interne est survenue. Veuillez réessayer plus tard.</p>';
        }
    }

    private function resolveRoute(string $method, string $path): array
    {
        foreach ($this->routes[$method] ?? [] as $route) {
            $matched = $this->match($route['path'], $path);
            if ($matched !== null) {
                return [$route['action'], $matched];
            }
        }

        return [null, []];
    }

    private function sanitizeLegacyPage(mixed $value): ?string
    {
        if (!is_string($value) || $value === '') {
            return null;
        }

        $page = strtolower(trim($value));
        if ($page === '') {
            return null;
        }

        return preg_replace('/[^a-z0-9\-]/', '', $page) ?: null;
    }

    private function logNotFoundRoute(string $method, string $uri, string $path, array $queryParams): void
    {
        $queryString = empty($queryParams) ? '' : ('?' . http_build_query($queryParams));
        error_log(sprintf('[router][404] method=%s uri=%s resolved_path=%s query=%s', $method, $uri, $path, $queryString));
    }

    private function fallbackToHomeOr404(string $method): void
    {
        [$homeAction] = $this->resolveRoute($method, '/');
        if ($homeAction !== null) {
            http_response_code(302);
            header('Location: /', true, 302);
            return;
        }

        http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        echo '404 - Route introuvable. Retournez à l’accueil : /';
    }

    private function match(string $routePath, string $currentPath): ?array
    {
        if ($routePath === $currentPath) {
            return [];
        }

        if (!str_contains($routePath, '{')) {
            return null;
        }

        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', static function (array $matches): string {
            return '(?P<' . $matches[1] . '>[^/]+)';
        }, $routePath);

        if ($pattern === null) {
            return null;
        }

        $regex = '#^' . $pattern . '$#';
        if (preg_match($regex, $currentPath, $matches) !== 1) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[] = urldecode((string) $value);
            }
        }

        return $params;
    }
}
