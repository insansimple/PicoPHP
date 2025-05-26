<?php

namespace Core;

class Router
{
    protected $routes = [];

    public function add($method, $uri, $action)
    {
        $this->routes[] = compact('method', 'uri', 'action');
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function dispatch($method, $uri)
    {
        $method = strtoupper($method);
        $uri = rtrim(parse_url($uri, PHP_URL_PATH), '/') ?: '/';

        foreach ($this->routes as $route) {
            // Pastikan $route['method'] array atau string
            $routeMethods = is_array($route['method']) ? array_map('strtoupper', $route['method']) : [strtoupper($route['method'])];
            $routeUri = rtrim($route['uri'], '/') ?: '/';

            if (in_array($method, $routeMethods) && $routeUri === $uri) {
                if (is_callable($route['action'])) {
                    return call_user_func($route['action']);
                } elseif (is_array($route['action'])) {
                    [$controller, $methodName] = $route['action'];
                    return $controller->$methodName();
                } else {
                    throw new \Exception("Invalid route action.");
                }
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
