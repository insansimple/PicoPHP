<?php

if (!function_exists('view')) {
    function view($name, $data = [])
    {
        $controller = new \Core\Controller();
        echo $controller->render_view($name, $data);
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        $controller = new \Core\Controller();
        $controller->redirect($url);
    }
}

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        static $config;

        if (!$config) {
            $config = require __DIR__ . '/../../config/config.php';
        }

        if ($key === null) {
            return $config;
        }

        // Support key dengan dot notation: config('db.host')
        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $segment) {
            if (is_array($value) && isset($value[$segment])) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }
}
