<?php

if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        $base = rtrim($_SERVER['SCRIPT_NAME'], '/index.php');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset($path)
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}
