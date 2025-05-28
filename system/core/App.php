<?php

namespace System\Core;

/**
 * Class App
 *
 * Kelas utama aplikasi yang bertanggung jawab untuk inisialisasi dan menjalankan aplikasi.
 * Ini menangani pemuatan helper global dan dispatching rute berdasarkan permintaan HTTP.
 */
class App
{
    /**
     * Konstruktor kelas App.
     *
     * Saat instance App dibuat, ia secara otomatis memuat semua fungsi helper
     * yang diperlukan oleh aplikasi.
     */
    public function __construct()
    {
        $this->load_helpers();

        $environment = config('environment') ?? 'development';
        $debug = config('debug') ?? true;
        $mysql_debug = config('mysql_debug') ?? true;

        // Di suatu tempat di awal aplikasi, setelah fungsi config() tersedia
        if ($debug) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0); // Sembunyikan error dari user
            ini_set('display_startup_errors', 0);
            error_reporting(E_ALL); // Tetap laporkan semua error untuk logging
            ini_set('log_errors', 1); // Pastikan error ditulis ke log file
            ini_set('error_log', __DIR__ . '/../logs/php_errors.log'); // Tentukan lokasi log PHP
        }

        if ($mysql_debug) {
            // Aktifkan debug MySQL jika diatur
            ini_set('mysqli.trace_mode', 1);
        } else {
            //tetap aktif tapi tulis dalam log
            ini_set('mysqli.trace_mode', 0);
            ini_set('mysqli.log_errors', 1); // Pastikan error MySQL ditulis ke log file
            ini_set('mysqli.error_log', __DIR__ . '/../logs/mysql_errors.log'); // Tentukan lokasi log MySQL
        }
    }

    /**
     * Menjalankan aplikasi.
     *
     * Metode ini memulai proses aplikasi dengan memuat dan mendispatch rute.
     */
    public function run()
    {
        // Memuat dan memproses definisi rute aplikasi
        $this->loadMiddleware();
        $this->loadRoutes();
    }

    /**
     * Memuat file rute dan mendispatch permintaan.
     *
     * Metode ini menginclude file `web.php` yang berisi definisi rute
     * aplikasi. Setelah rute dimuat, ia menggunakan objek Router global
     * untuk mencocokkan dan menjalankan aksi yang sesuai dengan metode
     * dan URI permintaan saat ini.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        // Menginclude file rute web.php.
        // File ini diharapkan mendefinisikan objek $router dan rute-rute di dalamnya.
        require __DIR__ . '/../routes/web.php';

        // Mendispatch permintaan saat ini menggunakan metode HTTP dan URI yang diterima.
        $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    }

    protected function loadMiddleware()
    {
        // Memuat file middleware.php yang berisi definisi middleware aplikasi.
        // File ini diharapkan mendefinisikan objek $middleware dan middleware yang digunakan.
        require __DIR__ . '/../routes/middleware.php';
    }

    /**
     * Memuat semua fungsi helper yang terletak di direktori 'library/helpers'.
     *
     * Metode ini mengiterasi melalui semua file PHP dalam direktori helper
     * dan menginclude-nya. Ini memastikan bahwa fungsi-fungsi helper global
     * seperti `view()`, `redirect()`, `config()`, `dd()`, `logs()`, `base_url()`, `asset()`,
     * dan `get_uri()` tersedia di seluruh aplikasi.
     *
     * @return void
     */
    private function load_helpers()
    {
        // Mencari semua file PHP di direktori 'library/helpers'.
        $helpers = glob(__DIR__ . '/../library/helpers/*.php');

        // Melakukan require_once untuk setiap file helper yang ditemukan.
        // `require_once` digunakan untuk mencegah error redeklarasi fungsi.
        foreach ($helpers as $helper) {
            require_once $helper;
        }
    }
}
