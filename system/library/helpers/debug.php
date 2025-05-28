<?php

/**
 * File ini berisi fungsi-fungsi helper global yang umum digunakan dalam aplikasi.
 * Fungsi-fungsi ini didefinisikan dalam kondisi `!function_exists()` untuk
 * mencegah redeklarasi jika file ini di-include beberapa kali atau jika
 * fungsi dengan nama yang sama sudah didefinisikan di tempat lain.
 */

if (!function_exists('dd')) {
    /**
     * Helper global untuk "dump and die".
     *
     * Fungsi ini digunakan untuk tujuan debugging. Ia akan mencetak (dump)
     * satu atau lebih variabel ke output (biasanya browser) dalam format yang mudah dibaca
     * menggunakan `var_dump()`, kemudian menghentikan eksekusi skrip (`die()`).
     * Ini sangat berguna untuk memeriksa isi variabel pada titik tertentu dalam kode.
     *
     * @param mixed ...$vars Satu atau lebih variabel yang ingin dicetak (dump).
     * @return void Eksekusi skrip akan dihentikan setelah pencetakan.
     */
    function dd(...$vars)
    {
        echo "<pre>"; // Memulai tag pre untuk format yang lebih baik di browser
        foreach ($vars as $var) {
            var_dump($var); // Mencetak isi variabel
        }
        echo "</pre>"; // Menutup tag pre
        die; // Menghentikan eksekusi skrip
    }
}

if (!function_exists('logs')) {
    /**
     * Helper global untuk menulis pesan ke file log aplikasi.
     *
     * Fungsi ini memungkinkan Anda untuk mencatat pesan ke file log `app.log`
     * yang terletak di direktori `logs/` di root aplikasi. Setiap entri log
     * akan menyertakan timestamp dan level log (misalnya 'info', 'warning', 'error').
     * Ini berguna untuk melacak alur aplikasi, error, atau informasi penting lainnya.
     *
     * @param string $message Pesan yang akan dicatat ke log.
     * @param string $level Level log pesan (misalnya 'info', 'warning', 'error', 'debug'). Default adalah 'info'.
     * @return void Pesan akan ditambahkan ke file log.
     */
    function logs($message, $level = 'info')
    {
        // Mendefinisikan path lengkap ke file log.
        $logFile = __DIR__ . '/../../logs/app.log';
        // Mendapatkan timestamp saat ini untuk entri log.
        $timestamp = date('Y-m-d H:i:s');
        // Memformat pesan log dengan timestamp dan level, diikuti dengan baris baru.
        $logMessage = "[$timestamp] [" . strtoupper($level) . "] $message" . PHP_EOL;

        // Menulis pesan ke file log. FILE_APPEND memastikan pesan ditambahkan, bukan menimpa.
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}
