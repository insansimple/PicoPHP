<?php

namespace System\Library;

/**
 * Class Mail
 *
 * Kelas utilitas statis untuk memfasilitasi pengiriman email.
 * Kelas ini mengintegrasikan dengan fungsi mail() bawaan PHP
 * dan mendukung pengiriman email dengan header kustom serta
 * penggunaan template view untuk konten email.
 */
class Mail
{
    /**
     * Mengirim email menggunakan fungsi mail() PHP.
     *
     * Metode ini mengambil konfigurasi email dari fungsi `config('mail')`
     * untuk menentukan default 'From' dan 'Reply-To'. Header kustom dapat
     * disediakan untuk menimpa atau menambahkan header default.
     *
     * @param string $to Alamat email penerima.
     * @param string $subject Subjek email.
     * @param string $message Konten pesan email (disarankan dalam format HTML).
     * @param array $headers Array asosiatif dari header email tambahan (misalnya ['Cc' => 'cc@example.com']).
     * @return bool True jika email berhasil dikirim, false sebaliknya.
     */
    public static function send($to, $subject, $message, $headers = [])
    {
        // Mengambil konfigurasi email dari sistem
        $config = config('mail');

        // Header default untuk email, termasuk 'From', 'Reply-To', 'X-Mailer', dan 'Content-type'.
        $defaultHeaders = [
            'From' => $config['from_name'] . " <{$config['from']}>",
            'Reply-To' => $config['reply_to'],
            'X-Mailer' => 'PHP/' . phpversion(),
            'Content-type' => 'text/html; charset=UTF-8' // Mengatur konten sebagai HTML dengan UTF-8
        ];

        // Menggabungkan header default dengan header kustom yang disediakan.
        // Header kustom akan menimpa header default jika ada kunci yang sama.
        $mergedHeaders = array_merge($defaultHeaders, $headers);

        // Mengkonversi array header menjadi format string yang dibutuhkan oleh fungsi mail().
        $headerString = '';
        foreach ($mergedHeaders as $key => $value) {
            $headerString .= "$key: $value\r\n";
        }

        // Mengirim email menggunakan fungsi mail() bawaan PHP.
        return mail($to, $subject, $message, $headerString);
    }

    /**
     * Mengirim email menggunakan template view sebagai konten pesan.
     *
     * Metode ini memungkinkan developer untuk menggunakan file view (PHP)
     * sebagai template untuk konten email. Data dapat diteruskan ke view
     * untuk dinamisasi konten.
     *
     * @param string $to Alamat email penerima.
     * @param string $subject Subjek email.
     * @param string $view Nama file view (tanpa ekstensi .php) yang berada di direktori 'app/Views/'.
     * @param array $data Data yang akan tersedia di dalam view sebagai variabel.
     * @param array $headers Array asosiatif dari header email tambahan.
     * @return bool True jika email berhasil dikirim, false sebaliknya.
     * @throws \Exception Jika file view email tidak ditemukan.
     */
    public static function sendTemplate($to, $subject, $view, $data = [], $headers = [])
    {
        // Merender view menjadi string HTML yang akan menjadi isi pesan email.
        $message = self::renderView($view, $data);

        // Memanggil metode send() untuk mengirim email dengan konten yang sudah dirender.
        return self::send($to, $subject, $message, $headers);
    }

    /**
     * Merender file view PHP menjadi string HTML.
     *
     * Metode ini secara internal digunakan untuk mengkonversi template view
     * menjadi konten email yang siap dikirim. Variabel dari array $data
     * akan diekstrak dan tersedia di dalam scope file view.
     *
     * @param string $view Nama file view (tanpa ekstensi .php).
     * @param array $data Data yang akan diekstrak dan tersedia di view.
     * @return string Konten HTML yang dirender dari view.
     * @throws \Exception Jika file view tidak ditemukan.
     */
    protected static function renderView($view, $data = [])
    {
        // Membangun path lengkap ke file view. Diasumsikan struktur: project_root/app/Views/
        $viewFile = __DIR__ . '/../../app/Views/' . $view . '.php';

        // Memeriksa apakah file view ada. Jika tidak, lemparkan pengecualian.
        if (!file_exists($viewFile)) {
            throw new \Exception("Email view '$view.php' not found.");
        }

        // Mengekstrak array $data menjadi variabel individual.
        // Misalnya, ['name' => 'John'] akan menjadi variabel $name = 'John';
        extract($data);

        // Memulai output buffering untuk menangkap output dari file view.
        ob_start();
        // Menginclude file view, yang akan menjalankan kode PHP di dalamnya
        // dan mencetak hasilnya ke buffer.
        include $viewFile;
        // Mengambil konten dari buffer dan membersihkan buffer.
        return ob_get_clean();
    }
}
