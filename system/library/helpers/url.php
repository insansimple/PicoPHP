<?php

/**
 * Nama File: url.php
 *
 * File ini berisi fungsi-fungsi helper global yang berkaitan dengan URL
 * dan penanganan URI dalam aplikasi. Fungsi-fungsi ini didefinisikan dalam
 * kondisi `!function_exists()` untuk mencegah redeklarasi.
 */

if (!function_exists('base_url')) {
    /**
     * Mengembalikan URL dasar (base URL) aplikasi.
     *
     * Fungsi ini menghasilkan URL dasar aplikasi, yang dapat digunakan untuk membangun
     * tautan atau path relatif. Ini dapat mengembalikan URL lengkap dengan skema dan host,
     * atau hanya path relatif tergantung pada parameter `$full`.
     *
     * @param string $path Path tambahan yang akan ditambahkan ke base URL (misalnya 'users/profile'). Default adalah string kosong.
     * @param bool $full Jika true, akan mengembalikan URL lengkap (misalnya 'https://example.com/myapp/path').
     * Jika false, akan mengembalikan path relatif dari root dokumen (misalnya '/myapp/path'). Default adalah false.
     * @return string URL dasar yang dihasilkan.
     */
    function base_url($path = '', $full = false)
    {
        // Logika untuk menghasilkan URL lengkap (dengan protokol dan host)
        if ($full) {
            // Menentukan protokol (http atau https)
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            // Mendapatkan nama host dari server
            $host = $_SERVER['HTTP_HOST'];
            // Membangun base URL dengan menghapus 'index.php' dari SCRIPT_NAME
            $base = rtrim($protocol . $host . $_SERVER['SCRIPT_NAME'], '/index.php');
            // Menggabungkan base URL dengan path yang diberikan
            return $base . '/' . ltrim($path, '/');
        }

        // Jika $full adalah false, hanya mengembalikan path relatif dari root dokumen
        // Menghapus 'index.php' dari SCRIPT_NAME untuk mendapatkan path dasar aplikasi
        return rtrim($_SERVER['SCRIPT_NAME'], '/index.php') . '/' . ltrim($path, '/');
    }
}


if (!function_exists('site_url')) {
    /**
     * Mengembalikan URL lengkap untuk rute aplikasi.
     *
     * Fungsi ini adalah alias untuk `base_url()` yang mengembalikan URL lengkap
     * dengan path yang diberikan. Ini berguna untuk membangun tautan ke rute
     * dalam aplikasi.
     *
     * @param string $path Path relatif ke rute aplikasi (misalnya 'users/profile').
     * @return string URL lengkap ke rute aplikasi.
     */
    function site_url($path = '')
    {
        // Menggunakan base_url untuk membangun URL ke rute aplikasi
        return base_url($path, true);
    }
}


if (!function_exists('asset')) {
    /**
     * Mengembalikan URL lengkap untuk aset (seperti CSS, JavaScript, gambar).
     *
     * Fungsi ini adalah shortcut untuk `base_url()` yang secara otomatis menambahkan
     * direktori 'assets/' di depan path yang diberikan. Ini membantu dalam mengelola
     * link ke file-file statis aplikasi.
     *
     * @param string $path Path relatif ke aset di dalam direktori 'assets/' (misalnya 'css/style.css').
     * @return string URL lengkap ke aset.
     */
    function asset($path)
    {
        // Menggunakan base_url untuk membangun URL ke aset
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('get_uri')) {
    /**
     * Mengambil segmen dari URI request saat ini.
     *
     * Fungsi ini mengurai URI dari request HTTP dan mengembalikan segmen URI
     * berdasarkan indeks yang diberikan. Ini berguna untuk routing dan
     * mengambil parameter dari URL.
     *
     * @param int|null $index Indeks berbasis 0 dari segmen URI yang ingin diambil.
     * Jika null, seluruh array segmen URI akan dikembalikan.
     * @return string|array|null Segmen URI pada indeks yang ditentukan,
     * array semua segmen URI jika $index adalah null,
     * atau null jika segmen tidak ditemukan pada indeks yang diberikan.
     */
    function get_uri(int $index = 0)
    {
        // Mengurai URI request dan menghapus leading/trailing slash.
        // PHP_URL_PATH hanya mengambil bagian path dari URL.
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        // Memecah URI menjadi segmen-segmen individual berdasarkan '/'.
        $segments = explode('/', $uri);

        // Jika $index adalah null, kembalikan seluruh array segmen.
        if ($index === null) {
            return $segments;
        }

        // Jika $index adalah numerik, coba kembalikan segmen pada indeks tersebut.
        if (is_numeric($index)) {
            return isset($segments[$index]) ? $segments[$index] : null;
        }

        // Mengembalikan null jika $index bukan numerik dan bukan null (bisa juga throw exception).
        return null;
    }
}
