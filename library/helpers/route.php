<?php

use Core\App;

if (!function_exists('route')) {
    function route(): \Core\Router
    {
        static $router = null;

        if (!$router) {
            // Ambil dari global App instance
            global $app;
            $router = $app->getRouter();
        }

        return $router;
    }
}

if (!function_exists('get')) {
    function get($uri, $action)
    {
        route()->add('GET', $uri, $action);
    }
}

if (!function_exists('post')) {
    function post($uri, $action)
    {
        route()->add('POST', $uri, $action);
    }
}

if (!function_exists('on')) {
    function on(array $methods, $uri, $action)
    {
        route()->add($methods, $uri, $action);
    }
}
