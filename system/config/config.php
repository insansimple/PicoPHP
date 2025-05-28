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
        'database'  => 'test',
        'username'  => 'root',
        'password'  => '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    // Mailer
    'mail' => [
        'from' => 'noreply@yourdomain.com',
        'from_name' => 'PicoPHP Mailer',
        'reply_to' => 'support@yourdomain.com',
    ],

    'security' => [
        'encryption_key' => 'your-encryption-key-here', // Ganti dengan kunci enkripsi yang aman
        'csrf_token' => true, // Aktifkan CSRF token
        'xss_protection' => false, // Aktifkan perlindungan XSS
        'csp' => "default-src 'self'; style-src 'self' 'unsafe-inline'", // Content Security Policy
    ],

    // Debug
    'debug'         => true,
    'mysql_debug'   => true, // Aktifkan debug MySQL
];
