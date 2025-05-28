<?php

namespace System\Core;

/**
 * Kelas Response bertanggung jawab untuk membangun dan mengirimkan respons HTTP
 * kembali ke klien. Ini menyediakan metode untuk mengatur kode status HTTP,
 * header, dan isi respons, serta konstanta untuk kode status HTTP umum.
 * Kelas ini mengimplementasikan pola Singleton.
 */
class Response
{
    // --- Konstanta Kode Status HTTP ---
    // Daftar konstanta ini menyediakan cara yang mudah dan mudah dibaca
    // untuk merujuk ke kode status HTTP yang berbeda,
    // meningkatkan keterbacaan kode dan mengurangi kesalahan ketik.

    const STATUS_OK = 200; // Permintaan berhasil.
    const STATUS_NOT_FOUND = 404; // Sumber daya tidak ditemukan.
    const STATUS_INTERNAL_SERVER_ERROR = 500; // Kesalahan server internal.
    const STATUS_BAD_REQUEST = 400; // Permintaan tidak valid.
    const STATUS_UNAUTHORIZED = 401; // Membutuhkan otentikasi.
    const STATUS_FORBIDDEN = 403; // Akses ditolak karena otorisasi tidak cukup.
    const STATUS_UNPROCESSABLE_ENTITY = 422; // Permintaan valid secara sintaksis, tetapi tidak dapat diproses.
    const STATUS_NO_CONTENT = 204; // Permintaan berhasil, tidak ada konten yang dikembalikan.
    const STATUS_CREATED = 201; // Sumber daya baru telah berhasil dibuat.
    const STATUS_ACCEPTED = 202; // Permintaan telah diterima untuk diproses, tetapi pemrosesan belum selesai.
    const STATUS_CONFLICT = 409; // Konflik permintaan (misalnya, konflik pengeditan).
    const STATUS_GONE = 410; // Sumber daya telah dihapus secara permanen.
    const STATUS_METHOD_NOT_ALLOWED = 405; // Metode HTTP yang digunakan tidak diizinkan untuk sumber daya.
    const STATUS_NOT_IMPLEMENTED = 501; // Server tidak mendukung fungsionalitas yang diperlukan untuk memenuhi permintaan.
    const STATUS_SERVICE_UNAVAILABLE = 503; // Server saat ini tidak dapat menangani permintaan karena kelebihan beban atau pemeliharaan.
    const STATUS_GATEWAY_TIMEOUT = 504; // Server bertindak sebagai gateway dan tidak menerima respons tepat waktu.
    const STATUS_TOO_MANY_REQUESTS = 429; // Pengguna telah mengirim terlalu banyak permintaan dalam waktu tertentu.
    const STATUS_BAD_GATEWAY = 502; // Server bertindak sebagai gateway dan menerima respons tidak valid dari server upstream.
    const STATUS_PRECONDITION_FAILED = 412; // Prekondisi yang ditentukan dalam header permintaan gagal dievaluasi.
    const STATUS_LENGTH_REQUIRED = 411; // Permintaan tidak menentukan panjang kontennya.
    const STATUS_EXPECTATION_FAILED = 417; // Server tidak dapat memenuhi ekspektasi yang ditunjukkan dalam header Expect permintaan.
    const STATUS_IM_A_TEAPOT = 418; // HTTP 418 I'm a teapot (RFC 7168) - Easter egg, tidak digunakan dalam produksi.
    const STATUS_UNAUTHORIZED_ACCESS = 403; // Alias untuk STATUS_FORBIDDEN.
    const STATUS_NOT_ACCEPTABLE = 406; // Sumber daya yang diminta tidak dapat diterima sesuai dengan header Accept yang dikirim dalam permintaan.
    const STATUS_UNSUPPORTED_MEDIA_TYPE = 415; // Entitas permintaan memiliki tipe media yang tidak didukung server atau sumber daya.

    // Properti statis untuk menyimpan instance tunggal dari kelas Response (Singleton Pattern).
    private static $instance = null;

    /**
     * Mendapatkan instance tunggal dari kelas Response.
     * Metode ini mengimplementasikan bagian dari pola Singleton.
     * Jika instance belum ada, maka akan dibuat instance baru. Jika sudah ada, instance yang sudah ada akan dikembalikan.
     *
     * @return Response Instance tunggal dari kelas Response.
     */
    public static function getInstance()
    {
        // Memeriksa apakah instance sudah diinisialisasi.
        if (self::$instance === null) {
            // Jika belum, buat instance baru dari kelas Response.
            self::$instance = new self();
        }
        // Mengembalikan instance yang sudah ada atau yang baru dibuat.
        return self::$instance;
    }

    /**
     * Mengirimkan respons HTTP dengan kode status dan pesan tertentu.
     * Secara otomatis mengatur header Content-Type ke application/json
     * dan mengkodekan pesan ke format JSON.
     *
     * @param int $status_code Kode status HTTP (misalnya, 200, 404, 500).
     * @param string $message Pesan atau data yang akan dikirimkan dalam respons.
     * @return void
     */
    public function send($status_code, $message)
    {
        // Mengatur kode status HTTP untuk respons.
        http_response_code($status_code);
        // Mengatur header Content-Type untuk menunjukkan bahwa respons adalah JSON.
        header('Content-Type: application/json');
        // Mengodekan array asosiatif yang berisi status dan pesan ke JSON
        // dan mencetaknya ke output.
        echo json_encode(['status' => $status_code, 'message' => $message]);
        // Penting: Menghentikan eksekusi skrip setelah mengirim respons
        // untuk mencegah output yang tidak diinginkan.
        exit();
    }

    /**
     * Mengirimkan respons JSON dengan data tertentu dan kode status opsional.
     * Secara otomatis mengatur header Content-Type ke application/json
     * dan mengodekan data ke format JSON.
     *
     * @param mixed $data Data yang akan dikirimkan dalam respons.
     * @param int $status_code Kode status HTTP (default 200 OK).
     * @return void
     */
    public function json($data, $status_code = 200)
    {
        // Mengatur kode status HTTP untuk respons.
        http_response_code($status_code);
        // Mengatur header Content-Type untuk menunjukkan bahwa respons adalah JSON.
        header('Content-Type: application/json');
        // Mengodekan data yang diberikan ke JSON dan mencetaknya ke output.
        echo json_encode($data);
        // Penting: Menghentikan eksekusi skrip setelah mengirim respons
        // untuk mencegah output yang tidak diinginkan.
        exit();
    }

    /**
     * Mengirimkan respons sukses (kode 200 OK) dengan data yang diberikan.
     * Ini adalah metode bantu untuk respons sukses standar.
     *
     * @param mixed $data Data yang akan dikirimkan dalam respons sukses.
     * @return void
     */
    public function success($data)
    {
        // Memanggil metode 'send' dengan kode status 200 dan data yang dibungkus dalam array 'data'.
        $this->send(self::STATUS_OK, ['data' => $data]);
    }

    /**
     * Mengirimkan respons kesalahan (kode 500 Internal Server Error) dengan pesan kesalahan.
     * Ini adalah metode bantu untuk respons kesalahan server standar.
     *
     * @param string $error_message Pesan kesalahan yang akan dikirimkan.
     * @return void
     */
    public function error($error_message)
    {
        // Memanggil metode 'send' dengan kode status 500 dan pesan kesalahan yang dibungkus dalam array 'error'.
        $this->send(self::STATUS_INTERNAL_SERVER_ERROR, ['error' => $error_message]);
    }
}
