<?php

namespace App\Libs;

/**
 * Simple Router Class
 * Handles routing and dispatches requests to controllers
 */
class Router
{
    private array $routes = [];
    private array $middlewares = [];
    
    /**
     * Add a GET route
     */
    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }
    
    /**
     * Add a POST route
     */
    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }
    
    /**
     * Add a route
     */
    private function addRoute(string $method, string $path, $handler, array $middleware): void
    {
        if (is_array($handler) && count($handler) === 2) {
            // [Controller::class, 'method'] format
            $controller = $handler[0];
            $action = $handler[1];
        } elseif (is_string($handler) && strpos($handler, '@') !== false) {
            // 'Controller@method' format
            [$controller, $action] = explode('@', $handler);
        } else {
            throw new \InvalidArgumentException('Invalid route handler format');
        }
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware
        ];
    }
    
    /**
     * Dispatch the request
     */
    public function dispatch(string $method, string $uri): void
    {
        $uri = $this->parseUri($uri);
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchRoute($route['path'], $uri)) {
                // Run middlewares
                foreach ($route['middleware'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    if (!$middleware->handle()) {
                        return;
                    }
                }
                
                // Dispatch to controller
                $controllerClass = $route['controller'];
                $action = $route['action'];
                
                if (!class_exists($controllerClass)) {
                    continue;
                }
                
                $controller = new $controllerClass();
                
                if (method_exists($controller, $action)) {
                    $params = $this->extractParams($route['path'], $uri);
                    call_user_func_array([$controller, $action], $params);
                    return;
                }
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        require __DIR__ . '/../../views/errors/404.php';
    }
    
    /**
     * Parse URI (remove query string, trim slashes)
     */
    private function parseUri(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = trim($uri, '/');
        
        // Remove public/ prefix if present
        $uri = str_replace('public/', '', $uri);
        $uri = trim($uri, '/');
        
        return $uri ?: '/';
    }
    
    /**
     * Match route pattern with URI
     */
    private function matchRoute(string $pattern, string $uri): bool
    {
        $pattern = trim($pattern, '/');
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        return (bool) preg_match($pattern, $uri);
    }
    
    /**
     * Extract parameters from URI
     */
    private function extractParams(string $pattern, string $uri): array
    {
        $pattern = trim($pattern, '/');
        $uri = trim($uri, '/');
        
        preg_match_all('/\{(\w+)\}/', $pattern, $paramNames);
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $pattern);
        
        if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
            array_shift($matches);
            return $matches;
        }
        
        return [];
    }
}

