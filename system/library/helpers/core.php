<?php

/**
 * File ini berisi fungsi-fungsi helper global yang umum digunakan dalam aplikasi.
 * Fungsi-fungsi ini didefinisikan dalam kondisi `!function_exists()` untuk
 * mencegah redeklarasi jika file ini di-include beberapa kali atau jika
 * fungsi dengan nama yang sama sudah didefinisikan di tempat lain.
 */

if (!function_exists('view')) {
    /**
     * Helper global untuk merender dan menampilkan view.
     *
     * Fungsi ini bertindak sebagai shortcut untuk memanggil metode `render` dari
     * objek `\Core\Controller`. Ini memudahkan pemanggilan view dari mana saja
     * dalam aplikasi tanpa perlu menginstansiasi Controller secara manual.
     *
     * @param string $name Nama file view (misalnya 'home/index' untuk 'app/Views/home/index.php').
     * @param array $data Array asosiatif data yang akan diteruskan dan tersedia di dalam view.
     * @return void Output HTML dari view langsung dicetak ke browser.
     */
    function view($name, $data = [])
    {
        // Menginstansiasi Controller untuk mengakses metode render.
        $controller = new System\Core\Controller();
        // Merender view dan mencetak output-nya.
        echo $controller->render($name, $data);
    }
}

if (!function_exists('redirect')) {
    /**
     * Helper global untuk melakukan pengalihan (redirect) HTTP.
     *
     * Fungsi ini mengirimkan header HTTP Redirect ke browser, mengarahkan pengguna
     * ke URL yang ditentukan. Fungsi ini juga memiliki penanganan khusus untuk
     * lingkungan CLI (Command Line Interface), biasanya untuk keperluan pengujian.
     *
     * @param string $url URL tujuan pengalihan.
     * @return object|void Dalam mode CLI, mengembalikan objek dengan status dan lokasi.
     * Dalam mode web, tidak mengembalikan apa-apa (melakukan redirect).
     */
    function redirect($url)
    {
        // Memeriksa apakah kode dijalankan dalam mode CLI (misalnya, untuk unit testing).
        if (php_sapi_name() === 'cli') {
            // Jika dalam mode CLI, kembalikan objek representasi redirect.
            // Ini mencegah pengalihan HTTP aktual terjadi selama pengujian.
            return (object)[
                'status' => 302, // Kode status redirect "Found"
                'location' => $url // URL tujuan
            ];
        }
        // Menginstansiasi Controller untuk mengakses metode redirect.
        $controller = new System\Core\Controller();
        // Melakukan pengalihan HTTP.
        $controller->redirect(base_url($url));
    }
}

if (!function_exists('config')) {
    /**
     * Helper global untuk mengakses nilai konfigurasi aplikasi.
     *
     * Fungsi ini memuat file konfigurasi (`config/config.php`) sekali dan
     * menyimpan nilai-nilainya secara statis untuk akses yang efisien di kemudian hari.
     * Mendukung akses ke nilai konfigurasi menggunakan dot notation (misalnya 'db.host').
     *
     * @param string|null $key Kunci konfigurasi yang ingin diambil (misalnya 'app.name', 'database.host').
     * Jika null, seluruh array konfigurasi akan dikembalikan.
     * @param mixed $default Nilai default yang akan dikembalikan jika kunci konfigurasi tidak ditemukan.
     * @return mixed Nilai konfigurasi yang diminta, seluruh array konfigurasi, atau nilai default.
     */
    function config($key = null, $default = null)
    {
        // Deklarasi variabel statis untuk menyimpan konfigurasi yang sudah dimuat.
        static $config;

        // Memuat file konfigurasi hanya jika belum dimuat.
        if (!$config) {
            // Path relatif ke file konfigurasi dari lokasi helper ini.
            $config = require __DIR__ . '/../../config/config.php';
        }

        // Jika tidak ada kunci yang diberikan, kembalikan seluruh array konfigurasi.
        if ($key === null) {
            return $config;
        }

        // Mendukung akses kunci dengan dot notation (misalnya 'db.host').
        $keys = explode('.', $key);
        $value = $config; // Mulai dengan array konfigurasi penuh.

        // Melakukan iterasi melalui segmen kunci untuk menelusuri array konfigurasi.
        foreach ($keys as $segment) {
            // Memeriksa apakah nilai saat ini adalah array dan apakah segmen kunci ada di dalamnya.
            if (is_array($value) && isset($value[$segment])) {
                $value = $value[$segment]; // Pindah ke segmen berikutnya dalam array.
            } else {
                return $default; // Jika segmen tidak ditemukan, kembalikan nilai default.
            }
        }

        // Mengembalikan nilai konfigurasi yang ditemukan.
        return $value;
    }
}
