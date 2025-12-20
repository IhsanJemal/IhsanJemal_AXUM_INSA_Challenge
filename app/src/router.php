<?php

/**
 * Minimal internal router
 * Used by index.php and a few internal redirects
 */

class Router
{
    private $routes = [];

    public function add(string $path, callable $handler)
    {
        $this->routes[$path] = $handler;
    }

    public function dispatch(string $path)
    {
        if (isset($this->routes[$path])) {
            return call_user_func($this->routes[$path]);
        }

        // Default 404
        http_response_code(404);
        echo "Page not found";
    }
}    
