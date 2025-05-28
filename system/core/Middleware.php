<?php

namespace System\Core;

// Mengimpor kelas Request dari namespace yang sama.
// Kelas ini kemungkinan besar bertanggung jawab untuk menangani permintaan HTTP yang masuk.
use System\Core\Request;
// Mengimpor kelas Response dari namespace yang sama.
// Kelas ini kemungkinan besar bertanggung jawab untuk membuat dan mengirimkan respons HTTP.
use System\Core\Response;
// Mengimpor kelas Closure. Closure sering digunakan untuk callback atau fungsi anonim.
use Closure;

/**
 * Kelas Middleware bertanggung jawab untuk mengelola dan menjalankan middleware.
 * Middleware adalah lapisan kode yang dapat memproses permintaan HTTP sebelum mencapai handler rute.
 * Kelas ini mengimplementasikan pola Singleton untuk memastikan hanya ada satu instance dari Middleware.
 */
class Middleware
{
    // Properti statis untuk menyimpan instance tunggal dari kelas Middleware (Singleton Pattern).
    private static $instance = null;

    /**
     * @var array $middleware
     * Menyimpan daftar middleware yang terdaftar.
     * Kunci array adalah alias (nama pendek) untuk middleware, dan nilainya adalah nama kelas lengkap (FQN) dari middleware tersebut.
     * Contoh:
     * 'auth' => "App\Middleware\AuthMiddleware",
     * 'log'  => "App\Middleware\LogMiddleware",
     */
    private $middleware = [];

    /**
     * Mendapatkan instance dari kelas Middleware.
     * Metode ini mengimplementasikan bagian dari pola Singleton.
     * Jika instance belum ada, maka akan dibuat instance baru. Jika sudah ada, instance yang sudah ada akan dikembalikan.
     *
     * @return Middleware Instance tunggal dari kelas Middleware.
     */
    public static function getInstance()
    {
        // Memeriksa apakah instance sudah diinisialisasi.
        if (self::$instance === null) {
            // Jika belum, buat instance baru dari kelas Middleware.
            self::$instance = new self();
        }
        // Mengembalikan instance yang sudah ada atau yang baru dibuat.
        return self::$instance;
    }

    /**
     * Menambahkan satu atau lebih middleware ke daftar middleware yang terdaftar.
     * Metode ini memungkinkan pendaftaran middleware dengan alias tertentu.
     *
     * @param array $middleware Sebuah array asosiatif berisi alias middleware dan nama kelasnya.
     * Contoh: ['auth' => 'App\Middleware\AuthMiddleware']
     * @return Middleware Mengembalikan instance Middleware saat ini (untuk chaining method).
     * @throws \InvalidArgumentException Jika format array middleware tidak sesuai (kunci atau nilai bukan string).
     */
    public function add(array $middleware)
    {
        // Melakukan iterasi pada setiap pasangan kunci-nilai dalam array middleware yang diberikan.
        foreach ($middleware as $key => $value) {
            // Memeriksa apakah kunci dan nilai adalah string.
            // Ini penting untuk memastikan bahwa alias dan nama kelas middleware valid.
            if (is_string($key) && is_string($value)) {
                // Menambahkan atau memperbarui entri middleware ke properti $this->middleware.
                $this->middleware[$key] = $value;
            } else {
                // Melemparkan pengecualian jika format input tidak valid.
                throw new \InvalidArgumentException("Middleware must be an associative array with string keys and values.");
            }
        }
        // Mengembalikan instance kelas saat ini untuk memungkinkan chaining method.
        return $this;
    }

    /**
     * Mendapatkan daftar semua middleware yang telah terdaftar.
     *
     * @return array Daftar middleware yang terdaftar (alias => nama kelas).
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * Menjalankan middleware tertentu berdasarkan aliasnya.
     * Metode ini akan mencari kelas middleware yang terkait dengan alias, membuat instance-nya,
     * dan memanggil metode `handle` pada instance tersebut.
     *
     * @param string $middleware Alias dari middleware yang akan dijalankan.
     * @return mixed Hasil dari metode `handle` middleware, yang diharapkan mengembalikan Request atau Response.
     * @throws \Exception Jika middleware tidak terdaftar, kelas middleware tidak ditemukan, atau metode `handle` tidak ada.
     */
    public function middleware(string $middleware)
    {
        // Memeriksa apakah alias middleware yang diminta ada dalam daftar middleware yang terdaftar.
        if (array_key_exists($middleware, $this->middleware)) {
            // Mendapatkan nama kelas lengkap dari middleware berdasarkan aliasnya.
            $middlewareClass = $this->middleware[$middleware];

            // Memeriksa apakah kelas middleware benar-benar ada.
            if (class_exists($middlewareClass)) {
                // Membuat instance baru dari kelas middleware.
                $middlewareInstance = new $middlewareClass();

                // Memeriksa apakah instance middleware memiliki metode 'handle'.
                // Metode 'handle' adalah konvensi untuk middleware, tempat logika utama middleware berada.
                if (method_exists($middlewareInstance, 'handle')) {
                    // Mendapatkan instance tunggal dari objek Request dan Response.
                    $request = Request::getInstance();
                    $response = Response::getInstance();

                    // Memanggil metode `handle` dari instance middleware.
                    // Metode `handle` diharapkan menerima objek Request, objek Response, dan sebuah Closure (next).
                    // Closure ini mewakili tindakan selanjutnya dalam rantai middleware.
                    // Dalam implementasi ini, Closure sederhana yang hanya mengembalikan objek Request.
                    return $middlewareInstance->handle($request, $response, function ($request) {
                        return $request;
                    });
                } else {
                    // Melemparkan pengecualian jika metode 'handle' tidak ditemukan di kelas middleware.
                    throw new \Exception("Tidak ditemukan method handle di class middleware: " . $middlewareClass);
                }
            } else {
                // Melemparkan pengecualian jika kelas middleware tidak ditemukan.
                throw new \Exception("Class Middleware  tidak ditemukan: " . $middlewareClass);
            }
        } else {
            // Melemparkan pengecualian jika alias middleware tidak terdaftar.
            throw new \Exception("Middleware tidak diatur: " . $middleware);
        }
    }
}
