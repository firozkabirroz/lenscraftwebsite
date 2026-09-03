<?php

namespace App\Support;

class Router
{
    /** @var array<string, array<int, array{pattern: string, handler: callable|string, params: string[]}>> */
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, callable|string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|string $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function any(string $path, callable|string $handler): void
    {
        $this->add('GET', $path, $handler);
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, callable|string $handler): void
    {
        $params = [];
        $pattern = preg_replace_callback('~\{(\w+)\}~', static function (array $m) use (&$params): string {
            $params[] = $m[1];

            return '([^/]+)';
        }, '/' . trim($path, '/'));

        $this->routes[$method][] = [
            'pattern' => '~^' . rtrim((string) $pattern, '/') . '/?$~',
            'handler' => $handler,
            'params'  => $params,
        ];
    }

    public function dispatch(string $method, string $path): void
    {
        $path = '/' . trim($path, '/');
        $method = $method === 'POST' ? 'POST' : 'GET';

        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                array_shift($matches);
                $this->call($route['handler'], $matches);

                return;
            }
        }

        abort(404, 'Page not found');
    }

    private function call(callable|string $handler, array $args): void
    {
        if (is_string($handler)) {
            [$class, $method] = explode('@', $handler);
            $class = 'App\\Controllers\\' . $class;
            $controller = new $class();
            $controller->{$method}(...$args);

            return;
        }

        $handler(...$args);
    }
}
