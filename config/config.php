<?php

return [
    // Aplikasi
    'app_name'     => 'PicoPHP',
    'base_url'     => 'http://localhost:8000', // Sesuaikan dengan host lokal kamu
    'environment'  => 'development', // atau production
    'timezone'     => 'Asia/Jakarta',

    // Database
    'db' => [
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'port'      => '3306',
        'database'  => 'picophp_db',
        'username'  => 'root',
        'password'  => '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    // Debug
    'debug'         => true,
];
