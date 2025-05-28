<?php

namespace System\Core;

use System\Core\Middleware;

/**
 * Class Controller
 *
 * Kelas dasar (base class) untuk semua controller dalam aplikasi.
 * Menyediakan fungsionalitas inti yang sering dibutuhkan oleh controller,
 * seperti merender view (menampilkan halaman HTML) dan melakukan pengalihan (redirect) HTTP.
 * Controller turunan dapat mewarisi kelas ini untuk mendapatkan fungsionalitas dasar tersebut.
 */
class Controller
{
    /**
     * Merender file view dan mengembalikan konten HTML-nya.
     *
     * Metode ini mengambil path relatif ke file view dan array data.
     * Data dalam array akan diekstrak menjadi variabel lokal yang dapat diakses
     * langsung di dalam file view. Output dari file view ditangkap menggunakan
     * output buffering dan dikembalikan sebagai string.
     *
     * @param string $view Nama file view (tanpa ekstensi .php) relatif terhadap direktori 'app/Views/'.
     * Contoh: 'home/index' akan merender 'app/Views/home/index.php'.
     * @param array $data Array asosiatif berisi data yang akan diteruskan ke view.
     * Kunci array akan menjadi nama variabel di dalam view.
     * @return string Konten HTML yang dirender dari view.
     * @throws \Exception Jika file view tidak ditemukan, respons 500 Internal Server Error akan dikirim.
     */
    public function render($view, $data = [])
    {
        // Membangun path lengkap ke file view. Diasumsikan struktur: project_root/app/Views/
        $viewFile = __DIR__ . '/../../app/Views/' . $view . '.php';

        // Memeriksa apakah file view ada.
        if (file_exists($viewFile)) {
            // Mengekstrak array $data menjadi variabel individual.
            // Contoh: $data = ['title' => 'My Page'] akan membuat variabel $title.
            extract($data);

            // Memulai output buffering untuk menangkap output dari file view.
            ob_start();

            // Membutuhkan (include) file view. Kode PHP di dalamnya akan dieksekusi,
            // dan output-nya (HTML, dll.) akan masuk ke buffer.
            require $viewFile;

            // Mengambil semua konten dari buffer dan membersihkan buffer.
            return ob_get_clean();
        } else {
            // Jika file view tidak ditemukan, atur kode respons HTTP ke 500
            // dan tampilkan pesan kesalahan.
            http_response_code(500);
            throw new \Exception("View file '$view.php' not found.");
            // Anda bisa juga throw new \Exception("View file '$view.php' tidak.");
            // untuk penanganan error yang lebih terstruktur.
        }
    }

    /**
     * Melakukan pengalihan (redirect) HTTP ke URL yang ditentukan.
     *
     * Fungsi ini mengirimkan header HTTP `Location` untuk mengarahkan browser klien
     * ke URL yang baru. Setelah mengirim header, skrip akan dihentikan (`exit;`)
     * untuk mencegah eksekusi kode lebih lanjut yang tidak diinginkan.
     * Fungsi ini juga memiliki penanganan khusus untuk lingkungan CLI,
     * yang berguna dalam pengujian unit.
     *
     * @param string $url URL tujuan pengalihan. Ini bisa berupa URL relatif atau absolut.
     * @return object|void Dalam mode CLI, mengembalikan objek dengan status dan lokasi.
     * Dalam mode web, fungsi ini akan menghentikan eksekusi skrip
     * setelah mengirim header redirect.
     */
    public function redirect($url)
    {
        // Memeriksa apakah kode dijalankan dalam mode CLI (Command Line Interface).
        // Ini biasanya terjadi selama unit testing, di mana pengalihan HTTP aktual
        // tidak dapat dilakukan atau tidak diinginkan.
        if (php_sapi_name() === 'cli') {
            // Dalam mode CLI, kembalikan objek yang merepresentasikan tindakan redirect.
            // Ini memungkinkan tes untuk memverifikasi bahwa redirect seharusnya terjadi
            // tanpa benar-benar melakukan redirect.
            return (object)[
                'status' => 302, // Kode status HTTP untuk "Found" (Redirect sementara)
                'location' => $url // URL tujuan redirect
            ];
        }

        // Jika dalam mode web, kirim header Location untuk melakukan redirect.
        header("Location: $url", true, 302);
        // Hentikan eksekusi skrip untuk memastikan redirect terjadi dan tidak ada
        // output lain yang dikirimkan.
        exit;
    }

    public function middleware(string $key_middleware)
    {
        $middleware = Middleware::getInstance();
        return $middleware->middleware($key_middleware);
    }
    // Fungsi lain yang dapat ditambahkan di sini meliputi:
    // - Metode untuk menangani input request (GET, POST).
    // - Metode untuk mengembalikan respons JSON.
    // - Implementasi middleware atau filter.
    // - Validasi data input.
}
