<?php
/**
 * Router — Lightweight HTTP router with parameter extraction — PHP 7.4 compatible
 * Supports: GET, POST, PUT, DELETE, OPTIONS
 */

declare(strict_types=1);

use App\Core\Response;

class Router
{
    /** @var Router|null */
    private static $instance = null;

    /** @var array */
    private $routes = [];

    /** @var array */
    private $middlewares = [];

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register a route: method + pattern + handler + optional middlewares
     * @param string $method
     * @param string $pattern
     * @param callable|array $handler
     * @param array $middlewares
     */
    public function addRoute(
        string $method,
        string $pattern,
        $handler,
        array $middlewares = []
    ): void {
        $this->routes[] = [
            'method'      => strtoupper($method),
            'pattern'     => $pattern,
            'handler'     => $handler,
            'middlewares' => $middlewares,
        ];
    }

    /** @param callable|array $handler */
    public function get(string $pattern, $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $pattern, $handler, $middlewares);
    }

    /** @param callable|array $handler */
    public function post(string $pattern, $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $pattern, $handler, $middlewares);
    }

    /** @param callable|array $handler */
    public function put(string $pattern, $handler, array $middlewares = []): void
    {
        $this->addRoute('PUT', $pattern, $handler, $middlewares);
    }

    /** @param callable|array $handler */
    public function delete(string $pattern, $handler, array $middlewares = []): void
    {
        $this->addRoute('DELETE', $pattern, $handler, $middlewares);
    }

    /** @param callable|array $handler */
    public function options(string $pattern, $handler, array $middlewares = []): void
    {
        $this->addRoute('OPTIONS', $pattern, $handler, $middlewares);
    }

    /**
     * Global middleware (runs before all routes)
     */
    public function middleware(callable $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Dispatch the current request
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri    = $_SERVER['REQUEST_URI'] ?? '/';

        // Strip query string
        $requestPath = strtok($requestUri, '?');

        // Remove base path if app is in a subdirectory (e.g., /CamLingua/Server)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && strpos($requestPath, $scriptName) === 0) {
            $requestPath = substr($requestPath, strlen($scriptName));
        }
        $requestPath = '/' . trim($requestPath, '/');

        // Handle CORS preflight
        if ($requestMethod === 'OPTIONS') {
            http_response_code(200);
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            exit;
        }

        // Run global middlewares
        foreach ($this->middlewares as $middleware) {
            call_user_func($middleware);
        }

        // Match route
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $params = $this->matchPattern($route['pattern'], $requestPath);
            if ($params === false) {
                continue;
            }

            // Route matched — run route-specific middlewares
            foreach ($route['middlewares'] as $middleware) {
                if (is_callable($middleware)) {
                    call_user_func($middleware);
                } elseif (is_string($middleware)) {
                    // Middleware class name
                    $mw = new $middleware();
                    $mw->handle();
                }
            }

            // Execute handler
            if (is_array($route['handler'])) {
                [$controllerClass, $method] = $route['handler'];
                $controller = new $controllerClass();
                // Pass params as a single array argument if present
                if (!empty($params)) {
                    call_user_func([$controller, $method], $params);
                } else {
                    call_user_func([$controller, $method]);
                }
            } else {
                if (!empty($params)) {
                    call_user_func($route['handler'], $params);
                } else {
                    call_user_func($route['handler']);
                }
            }

            return;
        }

        // No route matched — 404
        Response::notFound('Endpoint not found');
    }

    /**
     * Match a pattern like /api/users/{id} against /api/users/42
     * Returns extracted parameters or false if no match
     *
     * @param string $pattern
     * @param string $path
     * @return array|false
     */
    private function matchPattern(string $pattern, string $path)
    {
        // Convert {param} to named regex capture groups
        $regex = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return false;
        }

        // Extract only named captures
        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }
}
