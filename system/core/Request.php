<?php

namespace System\Core;

// Mengimpor kelas Upload dari namespace System\Library.
// Kelas ini kemungkinan besar digunakan untuk menangani proses upload file.
use System\Library\Upload;

/**
 * Kelas Request bertanggung jawab untuk mengelola dan menyediakan akses mudah
 * ke semua data yang terkait dengan permintaan HTTP yang masuk,
 * seperti metode HTTP, URI, parameter GET, data POST, header, dan file yang diunggah.
 * Kelas ini mengimplementasikan pola Singleton untuk memastikan hanya ada satu instance.
 */
class Request
{
    // Properti statis untuk menyimpan instance tunggal dari kelas Request (Singleton Pattern).
    private static $instance = null;

    // Properti privat untuk menyimpan informasi file yang sedang diproses.
    private $file = null;

    /**
     * Mendapatkan metode HTTP dari permintaan saat ini (GET, POST, PUT, DELETE, dll.).
     *
     * @return string Metode HTTP. Mengembalikan 'GET' sebagai default jika tidak dapat ditentukan.
     */
    public function method()
    {
        // Mengambil metode permintaan dari superglobal $_SERVER.
        // Menggunakan operator null coalescing (??) untuk memberikan nilai default 'GET'
        // jika 'REQUEST_METHOD' tidak disetel (misalnya, pada skrip yang dijalankan dari CLI).
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Mendapatkan instance tunggal dari kelas Request.
     * Metode ini mengimplementasikan bagian dari pola Singleton.
     * Jika instance belum ada, maka akan dibuat instance baru. Jika sudah ada, instance yang sudah ada akan dikembalikan.
     *
     * @return Request Instance tunggal dari kelas Request.
     */
    public static function getInstance()
    {
        // Memeriksa apakah instance sudah diinisialisasi.
        if (self::$instance === null) {
            // Jika belum, buat instance baru dari kelas Request.
            self::$instance = new self();
        }
        // Mengembalikan instance yang sudah ada atau yang baru dibuat.
        return self::$instance;
    }

    /**
     * Mendapatkan Uniform Resource Identifier (URI) dari permintaan saat ini.
     * URI adalah bagian dari URL setelah domain dan sebelum string kueri.
     * Contoh: Untuk 'http://example.com/blog/post?id=1', URI-nya adalah '/blog/post'.
     *
     * @return string URI yang diminta.
     */
    public function uri()
    {
        // Menggunakan parse_url() untuk mengurai URL lengkap dari $_SERVER['REQUEST_URI']
        // dan hanya mengambil komponen jalur (path).
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * Mendapatkan semua parameter kueri (query parameters) dari permintaan GET.
     * Parameter kueri adalah bagian dari URL setelah tanda tanya (?).
     * Contoh: Untuk 'http://example.com/search?q=php&sort=date', queryParams() akan mengembalikan ['q' => 'php', 'sort' => 'date'].
     *
     * @return array Parameter kueri sebagai array asosiatif.
     */
    public function queryParams()
    {
        // Mengembalikan superglobal $_GET yang secara otomatis diisi oleh PHP
        // dengan parameter kueri dari URL.
        return $_GET;
    }

    /**
     * Mendapatkan semua data dari permintaan POST.
     * Data POST biasanya dikirim dari formulir HTML dengan metode "POST".
     *
     * @return array Data POST sebagai array asosiatif.
     */
    public function postData()
    {
        // Mengembalikan superglobal $_POST yang secara otomatis diisi oleh PHP
        // dengan data yang dikirim melalui metode POST.
        return $_POST;
    }

    /**
     * Mendapatkan nilai dari parameter kueri tertentu.
     *
     * @param string $key Kunci (nama) dari parameter kueri yang ingin diambil.
     * @param mixed $default Nilai default yang akan dikembalikan jika kunci tidak ditemukan. Default-nya null.
     * @return mixed Nilai parameter kueri atau nilai default jika tidak ada.
     */
    public function get($key, $default = null)
    {
        // Menggunakan operator null coalescing (??) untuk mengembalikan nilai default
        // jika kunci tidak ada di array $_GET.
        return $_GET[$key] ?? $default;
    }

    /**
     * Mendapatkan nilai dari data POST tertentu.
     *
     * @param string $key Kunci (nama) dari data POST yang ingin diambil.
     * @param mixed $default Nilai default yang akan dikembalikan jika kunci tidak ditemukan. Default-nya null.
     * @return mixed Nilai data POST atau nilai default jika tidak ada.
     */
    public function post($key, $default = null)
    {
        // Menggunakan operator null coalescing (??) untuk mengembalikan nilai default
        // jika kunci tidak ada di array $_POST.
        return $_POST[$key] ?? $default;
    }

    /**
     * Mendapatkan nilai dari parameter permintaan secara umum (dari GET, POST, atau cookie).
     * Urutan prioritasnya adalah variabel lingkungan (environment), GET, POST, cookie.
     *
     * @param string $key Kunci (nama) dari parameter permintaan yang ingin diambil.
     * @param mixed $default Nilai default yang akan dikembalikan jika kunci tidak ditemukan. Default-nya null.
     * @return mixed Nilai parameter permintaan atau nilai default jika tidak ada.
     */
    public function request($key, $default = null)
    {
        // Menggunakan operator null coalescing (??) untuk mengembalikan nilai default
        // jika kunci tidak ada di array $_REQUEST.
        return $_REQUEST[$key] ?? $default;
    }

    /**
     * Mendapatkan nilai dari header HTTP tertentu dari permintaan.
     *
     * @param string $header Nama header yang ingin diambil (misalnya, 'Content-Type', 'User-Agent').
     * @return string|null Nilai header atau null jika header tidak ditemukan.
     */
    public function header($header)
    {
        // Mengubah nama header ke format yang digunakan oleh $_SERVER (misalnya, 'Content-Type' menjadi 'HTTP_CONTENT_TYPE').
        // Semua header HTTP dari permintaan diawali dengan 'HTTP_' di array $_SERVER,
        // dan tanda hubung diganti dengan garis bawah, lalu diubah menjadi huruf besar.
        $header = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        // Mengembalikan nilai header atau null jika tidak ada.
        return $_SERVER[$header] ?? null;
    }

    /**
     * Mendapatkan semua header HTTP dari permintaan.
     *
     * @return array Semua header sebagai array asosiatif (nama_header_lowercase_dengan_strip => nilai).
     */
    public function headers()
    {
        $headers = [];
        // Melakukan iterasi pada superglobal $_SERVER.
        foreach ($_SERVER as $key => $value) {
            // Memeriksa apakah kunci dimulai dengan 'HTTP_', yang menunjukkan itu adalah header HTTP.
            if (strpos($key, 'HTTP_') === 0) {
                // Membersihkan nama header (menghapus 'HTTP_', mengubah garis bawah menjadi strip, dan lowercase).
                $headerName = str_replace('_', '-', strtolower(substr($key, 5)));
                // Menambahkan header ke array $headers.
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    /**
     * Memeriksa apakah permintaan saat ini adalah permintaan AJAX.
     * Ini biasanya ditentukan dengan memeriksa header 'X-Requested-With' yang dikirim oleh JavaScript
     * (misalnya, jQuery, Axios, Fetch API).
     *
     * @return bool True jika permintaan adalah AJAX, false jika tidak.
     */
    public function isAjax()
    {
        // Memeriksa nilai header 'X-Requested-With'.
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Memeriksa apakah permintaan saat ini dikirim melalui koneksi aman (HTTPS).
     *
     * @return bool True jika permintaan menggunakan HTTPS, false jika tidak.
     */
    public function isSecure()
    {
        // Memeriksa keberadaan dan nilai dari $_SERVER['HTTPS'].
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    }

    /**
     * Mendapatkan semua file yang diunggah dalam permintaan.
     *
     * @return array Superglobal $_FILES yang berisi informasi tentang file yang diunggah.
     */
    public function files()
    {
        // Mengembalikan superglobal $_FILES yang diisi oleh PHP dengan informasi file yang diunggah.
        return $_FILES;
    }

    /**
     * Mendapatkan objek Upload untuk file tertentu yang diunggah.
     *
     * @param string $key Kunci (nama input) dari file yang ingin diambil.
     * @return Upload Instance dari kelas Upload yang telah diinisialisasi dengan file yang diminta.
     * @throws \Exception Jika file dengan kunci yang diberikan tidak ditemukan atau tidak diunggah dengan benar.
     */
    public function file($key)
    {
        // Mendapatkan instance tunggal dari kelas Upload.
        $upload = Upload::getInstance();

        // Memeriksa apakah file dengan kunci yang diberikan ada dan diunggah tanpa kesalahan.
        if (!$this->hasFile($key)) {
            // Melemparkan pengecualian jika file tidak ditemukan atau ada masalah upload.
            throw new \Exception("File dengan key '$key' tidak ditemukan atau tidak di upload dengan benar.");
        }

        // Menyimpan informasi file yang diunggah ke properti privat $file.
        $this->file = $_FILES[$key];
        // Menetapkan file ke instance Upload agar dapat diproses lebih lanjut oleh kelas Upload.
        $upload->setFile($this->file);

        // Mengembalikan instance Upload yang siap digunakan untuk memanipulasi file.
        return $upload;
    }

    /**
     * Memeriksa apakah ada file yang diunggah dengan kunci tertentu dan apakah pengunggahan berhasil.
     *
     * @param string $key Kunci (nama input) dari file yang ingin diperiksa.
     * @return bool True jika file ada dan diunggah tanpa kesalahan, false jika tidak.
     */
    public function hasFile($key)
    {
        // Memeriksa apakah kunci file ada di $_FILES dan apakah tidak ada kesalahan upload (UPLOAD_ERR_OK).
        return isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK;
    }
}
