<?php

namespace System\Library;

/**
 * Class Time
 *
 * Kelas utilitas statis untuk mengelola operasi terkait waktu dan tanggal.
 * Menyediakan fungsionalitas untuk mendapatkan waktu saat ini, timestamp,
 * format tanggal, menghitung perbedaan waktu, dan menampilkan waktu "sejak".
 */
class Time
{
    /**
     * @var string $timezone Menyimpan zona waktu yang dikonfigurasi untuk aplikasi.
     */
    protected static $timezone;

    /**
     * Inisialisasi statis.
     *
     * Metode ini dipanggil secara otomatis saat kelas pertama kali diakses.
     * Bertanggung jawab untuk mengatur zona waktu default berdasarkan konfigurasi
     * aplikasi atau fallback ke 'UTC' jika tidak ada konfigurasi yang ditemukan.
     */
    protected static function init()
    {
        if (!self::$timezone) {
            // Mengambil zona waktu dari konfigurasi aplikasi, default ke 'UTC'
            self::$timezone = config('timezone', 'UTC');
            // Mengatur zona waktu default PHP jika fungsi date_default_timezone_set tersedia
            if (function_exists('date_default_timezone_set')) {
                date_default_timezone_set(self::$timezone);
            }
        }
    }

    /**
     * Mengembalikan tanggal dan waktu saat ini dalam format yang ditentukan.
     *
     * @param string $format Format tanggal dan waktu yang diinginkan (misalnya 'Y-m-d H:i:s').
     * Default adalah 'Y-m-d H:i:s'.
     * @return string Tanggal dan waktu saat ini yang diformat.
     */
    public static function now($format = 'Y-m-d H:i:s')
    {
        self::init(); // Memastikan zona waktu diinisialisasi
        return date($format);
    }

    /**
     * Mengembalikan timestamp Unix saat ini.
     *
     * @return int Timestamp Unix saat ini.
     */
    public static function timestamp()
    {
        self::init(); // Memastikan zona waktu diinisialisasi
        return time();
    }

    /**
     * Memformat timestamp atau string waktu menjadi format tanggal dan waktu yang ditentukan.
     *
     * @param int|string $timestamp Timestamp Unix atau string tanggal/waktu yang dapat diurai.
     * @param string $format Format tanggal dan waktu yang diinginkan (misalnya 'Y-m-d H:i:s').
     * Default adalah 'Y-m-d H:i:s'.
     * @return string Tanggal dan waktu yang diformat.
     * @throws \InvalidArgumentException Jika timestamp yang diberikan tidak valid.
     */
    public static function format($timestamp, $format = 'Y-m-d H:i:s')
    {
        self::init(); // Memastikan zona waktu diinisialisasi

        // Mengkonversi timestamp atau string ke integer timestamp
        $time = is_numeric($timestamp) ? (int) $timestamp : strtotime($timestamp);

        // Melemparkan pengecualian jika konversi gagal
        if ($time === false) {
            throw new \InvalidArgumentException("Invalid timestamp: $timestamp");
        }

        return date($format, $time);
    }

    /**
     * Menghitung perbedaan waktu dalam detik antara dua string waktu.
     *
     * @param string $start String waktu awal yang dapat diurai (misalnya '2023-01-01 10:00:00').
     * @param string $end String waktu akhir yang dapat diurai (misalnya '2023-01-01 11:30:00').
     * @return int Perbedaan waktu dalam detik (selalu nilai absolut).
     * @throws \InvalidArgumentException Jika salah satu atau kedua string waktu tidak valid.
     */
    public static function diff($start, $end)
    {
        // Mengkonversi string waktu ke timestamp Unix
        $startTime = strtotime($start);
        $endTime = strtotime($end);

        // Melemparkan pengecualian jika konversi gagal
        if ($startTime === false || $endTime === false) {
            throw new \InvalidArgumentException("Invalid start or end time.");
        }

        // Mengembalikan nilai absolut dari perbedaan
        return abs($endTime - $startTime);
    }

    /**
     * Mengembalikan representasi "sejak" dari timestamp yang diberikan (misalnya "5 menit yang lalu").
     *
     * @param string $timestamp String waktu yang dapat diurai.
     * @return string Representasi waktu "sejak" atau "invalid time" jika input tidak valid.
     */
    public static function ago($timestamp)
    {
        self::init(); // Memastikan zona waktu diinisialisasi

        // Mengkonversi string waktu ke timestamp Unix
        $time = strtotime($timestamp);
        // Mengembalikan 'invalid time' jika konversi gagal
        if ($time === false) return 'invalid time';

        // Menghitung perbedaan dalam detik
        $diff = time() - $time;

        // Menentukan representasi "sejak" berdasarkan perbedaan
        if ($diff < 60) {
            return 'just now';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' minutes ago';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' hours ago';
        } elseif ($diff < 604800) { // Kurang dari 7 hari
            return floor($diff / 86400) . ' days ago';
        } else {
            return date('Y-m-d', $time); // Mengembalikan tanggal jika lebih dari 7 hari
        }
    }

    /**
     * Memformat timestamp berdasarkan lokal negara tertentu menggunakan IntlDateFormatter.
     *
     * Metode ini menyediakan fungsionalitas format tanggal yang lebih canggih
     * dengan mempertimbangkan konvensi lokal negara, seperti urutan hari, bulan, tahun.
     * Membutuhkan ekstensi PHP intl diaktifkan.
     *
     * @param int|string $timestamp Timestamp Unix atau string tanggal/waktu yang dapat diurai.
     * @param string $country Kode negara 2 huruf (misalnya 'ID', 'US', 'GB'). Default 'ID'.
     * @param string $format Pola format tanggal/waktu ICU (misalnya 'yyyy-MM-dd HH:mm:ss').
     * Default adalah 'yyyy-MM-dd HH:mm:ss'.
     * @return string Tanggal dan waktu yang diformat sesuai lokal negara.
     * @throws \InvalidArgumentException Jika timestamp yang diberikan tidak valid.
     * @throws \Exception Jika ekstensi intl tidak diaktifkan.
     */
    public static function countryFormat($timestamp, $country = 'ID', $format = 'yyyy-MM-dd HH:mm:ss')
    {
        // Memastikan ekstensi intl tersedia
        if (!class_exists('\IntlDateFormatter')) {
            throw new \Exception("PHP intl extension belum di enable. Enable terlebih dahulu sebelum menggunakan " . __METHOD__);
        }

        // Mengkonversi timestamp atau string ke integer timestamp
        $time = is_numeric($timestamp) ? (int) $timestamp : strtotime($timestamp);
        // Melemparkan pengecualian jika konversi gagal
        if ($time === false) {
            throw new \InvalidArgumentException("Invalid timestamp: $timestamp");
        }

        // Pemetaan kode negara ke lokal ICU
        $locales = [
            'ID' => 'id_ID',
            'US' => 'en_US',
            'GB' => 'en_GB',
            'FR' => 'fr_FR',
            'DE' => 'de_DE',
            // Tambahkan lebih banyak lokal sesuai kebutuhan
        ];

        // Mendapatkan lokal yang sesuai, default ke 'en_US' jika tidak ditemukan
        $locale = $locales[strtoupper($country)] ?? 'en_US';

        // Membuat objek IntlDateFormatter untuk memformat tanggal
        $fmt = new \IntlDateFormatter(
            $locale,
            \IntlDateFormatter::FULL, // Gaya tanggal: Penuh
            \IntlDateFormatter::FULL, // Gaya waktu: Penuh
            date_default_timezone_get(), // Menggunakan zona waktu default PHP
            \IntlDateFormatter::GREGORIAN, // Menggunakan kalender Gregorian
            $format // Pola format yang ditentukan
        );

        return $fmt->format($time);
    }
}
