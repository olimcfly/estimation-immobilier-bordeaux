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

        [$action, $params] = $this->resolveRoute($method, $path);

        if ($action === null) {
            http_response_code(404);
            echo '404 - Route introuvable';
            return;
        }

        [$controllerClass, $controllerMethod] = $action;
        $controller = new $controllerClass();
        $controller->{$controllerMethod}(...$params);
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
