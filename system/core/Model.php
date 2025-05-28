<?php

namespace System\Core;

// Memastikan kelas Database tersedia sebelum digunakan
require_once __DIR__ . '/../Library/Database.php';

use System\Library\Database;

/**
 * Class Model
 *
 * Kelas abstrak dasar untuk semua model dalam aplikasi.
 * Kelas ini menyediakan fungsionalitas interaksi database dasar
 * seperti mengambil data, menghitung record, dan mencari berdasarkan ID atau kolom.
 * Setiap model yang mewarisi kelas ini diharapkan memiliki properti `$table`
 * yang mendefinisikan tabel database yang sesuai.
 */
abstract class Model
{
    /**
     * @var string $table Nama tabel database yang terkait dengan model ini.
     * Properti ini harus didefinisikan di setiap kelas model turunan.
     */
    protected $table;

    /**
     * @var Database $db Instance dari kelas Database untuk interaksi database.
     */
    protected $db;

    /**
     * Konstruktor untuk kelas Model.
     *
     * Menginisialisasi properti `$db` dengan instance tunggal (singleton) dari
     * kelas `Library\Database`. Ini memastikan bahwa semua model berbagi
     * koneksi database yang sama.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Mengambil semua data dari tabel model.
     *
     * Mendukung paginasi opsional dengan parameter `$limit` dan `$offset`.
     *
     * @param int $limit Batas jumlah record yang akan diambil. Jika 0, semua record akan diambil.
     * @param int $offset Offset (jumlah record yang akan dilewati) sebelum mengambil data.
     * @return array Array objek yang mewakili baris data dari tabel.
     */
    public function data($limit = 0, $offset = 0)
    {
        if ($limit > 0) {
            // Mengambil data dengan LIMIT dan OFFSET
            return $this->db->query("SELECT * FROM {$this->table} LIMIT {$limit} OFFSET {$offset}")->resultSet();
        } else {
            // Mengambil semua data tanpa LIMIT
            return $this->db->query("SELECT * FROM {$this->table}")->resultSet();
        }
    }

    /**
     * Menghitung total jumlah record dalam tabel model.
     *
     * @return int Total jumlah record. Mengembalikan 0 jika tidak ada record atau terjadi kesalahan.
     */
    public function count()
    {
        // Menjalankan query untuk menghitung total record
        $result = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}")->single();
        // Mengembalikan nilai total, default 0 jika null atau tidak ada
        return intval($result->total ?? 0);
    }

    /**
     * Mencari satu record berdasarkan kolom 'id'.
     *
     * Mengembalikan record pertama yang cocok dengan ID yang diberikan.
     *
     * @param int $id ID dari record yang akan dicari.
     * @return object|null Objek yang mewakili record yang ditemukan, atau null jika tidak ditemukan.
     */
    public function find($id)
    {
        // Menjalankan query untuk mencari berdasarkan ID.
        // Menggunakan binding parameter 'i' untuk integer.
        $result = $this->db->query("SELECT * FROM {$this->table} WHERE id = ?")->bind(["i", $id])->resultSet();
        // Mengembalikan elemen pertama dari hasil (jika ada), atau null.
        return $result[0] ?? null;
    }

    /**
     * Mencari record berdasarkan kolom dan nilai tertentu.
     *
     * Mengembalikan semua record yang cocok dengan kriteria kolom-nilai yang diberikan.
     *
     * @param string $column Nama kolom untuk dicari.
     * @param mixed $value Nilai yang akan dicocokkan dalam kolom.
     * @return array|null Array objek yang mewakili record yang ditemukan, atau null jika tidak ditemukan.
     */
    public function findBy($column, $value)
    {
        // Menjalankan query untuk mencari berdasarkan kolom dan nilai.
        // Menggunakan binding parameter 's' secara default untuk string.
        // Perhatikan bahwa untuk tipe data lain, 's' mungkin perlu disesuaikan atau diperiksa.
        $result = $this->db->query("SELECT * FROM {$this->table} WHERE {$column} = ?")->bind(['s' => $value])->resultSet();
        // Mengembalikan hasil (bisa berupa array kosong jika tidak ditemukan).
        return $result ?? null;
    }
}
