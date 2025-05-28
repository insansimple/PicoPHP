<?php

namespace System\Library; // Sesuaikan namespace jika diperlukan

use mysqli;
use mysqli_sql_exception; // Tetap gunakan ini untuk error spesifik MySQLi

use function PHPUnit\Framework\throwException;

class Database
{
    private static $instance = null; // Untuk Singleton Pattern
    private $connection;
    private $stmt;
    private $error; // Tetap bisa digunakan untuk menyimpan pesan error, meskipun kita melempar exception
    private $lastQuery;

    /**
     * Konstruktor privat untuk Singleton Pattern.
     * Menginisialisasi koneksi database.
     *
     * @throws \Exception Jika koneksi database gagal.
     */
    private function __construct()
    {
        // Ambil konfigurasi database
        $host = config('db.host');
        $username = config('db.username');
        $password = config('db.password');
        $database = config('db.database');
        $port = config('db.port', 3306); // Default port 3306

        try {
            $this->connection = new mysqli($host, $username, $password, $database, $port);

            // Set charset
            $this->connection->set_charset(config('db.charset', 'utf8mb4'));

            // Cek koneksi
            if ($this->connection->connect_error) {
                // Melempar exception jika ada kesalahan koneksi
                throw new \Exception('Connection failed: ' . $this->connection->connect_error);
            }
        } catch (mysqli_sql_exception $e) {
            // Tangkap mysqli_sql_exception dan lemparkan sebagai generic \Exception
            $this->error = $e->getMessage();
            throw new \Exception("Database Connection Error: " . $this->error, $e->getCode(), $e);
        } catch (\Exception $e) {
            // Tangkap exception umum lainnya jika ada
            $this->error = $e->getMessage();
            throw new \Exception("An unexpected database error occurred: " . $this->error, $e->getCode(), $e);
        }
    }

    private function __clone()
    {
        // Mencegah cloning instance
    }

    /**
     * Mendapatkan instance tunggal dari kelas Database (Singleton Pattern).
     *
     * @return Database
     * @throws \Exception Jika koneksi database gagal saat pertama kali instansiasi.
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * Mendapatkan koneksi database.
     * 
     * @return mysqli
     * @throws \Exception Jika koneksi database gagal.
     */
    public function getConnection()
    {
        if (!$this->connection || $this->connection->connect_error) {
            throw new \Exception("Database connection is not available.");
        }
        return $this->connection;
    }


    /**
     * Menjalankan query SQL.
     *
     * @param string $sql Query SQL yang akan dijalankan.
     * @return $this
     * @throws \Exception Jika persiapan query gagal.
     */
    public function query(string $sql)
    {
        $this->lastQuery = $sql; // Simpan query terakhir untuk debugging
        $this->stmt = $this->connection->prepare($sql);

        if ($this->stmt === false) {
            // Melempar exception jika persiapan query gagal
            $this->error = 'Prepare failed: ' . $this->connection->error . ' (SQL: ' . $sql . ')';
            throw new \Exception("Query Preparation Error: " . $this->error);
        }
        return $this;
    }

    /**
     * Mengikat parameter ke prepared statement.
     *
     * @param array $params Array dengan format ['tipe', nilai1, nilai2, ...]. 
     *                      Contoh: ['ssi', 'nama', 'email@example.com', 25]
     *                      Tipe: 's' (string), 'i' (integer), 'd' (double), 'b' (blob)
     * 
     * @return $this
     * @throws \Exception Jika statement belum disiapkan atau proses binding gagal.
     */

    public function bind(array $params = [])
    {
        if (empty($params) || $this->stmt === null) {
            throw new \Exception("Tidak ada parameter untuk di-bind atau statement belum disiapkan");
        }

        // Ambil tipe di index 0
        $types = array_shift($params); // Ambil string 'ssi'
        $values = $params;

        if (strlen($types) !== count($values)) {
            throw new \Exception("Jumlah type tidak cocok dengan jumlah parameter.");
        }

        $refs = [];
        foreach ($values as $key => $value) {
            $refs[$key] = &$values[$key];
        }

        if (!call_user_func_array([$this->stmt, 'bind_param'], array_merge([$types], $refs))) {
            $this->error = 'Binding parameters failed: ' . $this->stmt->error;
            throw new \Exception("Parameter Binding Error: " . $this->error);
        }

        return $this;
    }


    /**
     * Menjalankan statement yang sudah dipersiapkan.
     *
     * @return bool True jika berhasil.
     * @throws \Exception Jika eksekusi statement gagal.
     */
    public function execute()
    {
        if ($this->stmt === null) {
            throw new \Exception("No statement prepared to execute.");
        }
        if (!$this->stmt->execute()) {
            $this->error = 'Execution failed: ' . $this->stmt->error;
            throw new \Exception("Query Execution Error: " . $this->error);
        }
        return true;
    }

    /**
     * Mengambil hasil SELECT sebagai array objek.
     *
     * @return array Array objek hasil query.
     * @throws \Exception Jika gagal mendapatkan hasil.
     */
    public function resultSet()
    {
        $this->execute();
        $result = $this->stmt->get_result();
        if ($result === false) {
            throw new \Exception("Failed to get result set: " . $this->stmt->error);
        }
        return $result->fetch_all(MYSQLI_ASSOC); // Mengambil sebagai array asosiatif
    }

    /**
     * Mengambil satu baris hasil SELECT sebagai objek.
     *
     * @return object|null Satu baris hasil query sebagai objek, atau null jika tidak ada.
     * @throws \Exception Jika gagal mendapatkan hasil.
     */
    public function single()
    {
        $this->execute();
        $result = $this->stmt->get_result();
        if ($result === false) {
            throw new \Exception("Failed to get single result: " . $this->stmt->error);
        }
        return $result->fetch_object(); // Mengambil sebagai objek
    }

    /**
     * Mengambil jumlah baris yang terpengaruh oleh operasi INSERT, UPDATE, DELETE.
     *
     * @return int Jumlah baris yang terpengaruh.
     */
    public function rowCount()
    {
        if ($this->stmt === null) {
            return 0; // Atau throw exception jika ingin lebih ketat
        }
        return $this->stmt->affected_rows;
    }

    /**
     * Mendapatkan ID dari baris yang baru saja di-INSERT.
     *
     * @return int ID terakhir yang di-generate.
     */
    public function lastInsertId()
    {
        return $this->connection->insert_id;
    }

    /**
     * Membangun dan menjalankan query INSERT.
     *
     * @param string $table Nama tabel.
     * @param array $data Array asosiatif data yang akan di-INSERT (kolom => nilai).
     * @return int ID terakhir yang di-INSERT, atau 0 jika gagal.
     * @throws \Exception Jika operasi INSERT gagal.
     */
    public function insert(string $table, array $data)
    {
        if (empty($data)) {
            throw new \Exception("Data for insert cannot be empty.");
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->lastQuery = $sql; // Simpan query terakhir untuk debugging
        $this->query($sql);

        // Pastikan parameter diikat dengan tipe yang benar
        $this->bind($this->getParamTypesAsArray($data)); // Menggunakan helper baru
        $this->execute();

        return $this->lastInsertId();
    }

    /**
     * Menjalankan query UPDATE menggunakan prepared statement yang aman.
     *
     * @param string $table Nama tabel yang akan diperbarui.
     * @param array $data Array asosiatif berisi kolom dan nilai baru (misal: ['nama' => 'Insan', 'umur' => 35]).
     * @param string $where Klausa WHERE sebagai kondisi pembaruan (misal: "id = ?").
     * @param array $whereParams Nilai-nilai parameter untuk klausa WHERE (misal: [1] untuk "id = ?"). Kosong jika tidak ada parameter.
     * @return int Jumlah baris yang berhasil diperbarui.
     * @throws \Exception Jika data kosong, klausa WHERE kosong, atau eksekusi gagal.
     */
    public function update(string $table, array $data, string $where, array $whereParams = [])
    {
        if (empty($data)) {
            throw new \Exception("Data for update cannot be empty.");
        }
        if (empty($where)) {
            throw new \Exception("WHERE clause for update cannot be empty.");
        }

        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = ?";
        }
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $this->lastQuery = $sql; // Simpan query terakhir untuk debugging
        $this->query($sql);

        // Gabungkan parameter data dan parameter where
        $allValues = array_merge(array_values($data), array_values($whereParams));
        $this->bind($this->getParamTypesAsArray($allValues)); // Menggunakan helper baru

        $this->execute();

        return $this->rowCount();
    }

    /**
     * Menjalankan query DELETE dengan prepared statement yang aman.
     *
     * @param string $table Nama tabel yang akan dihapus datanya.
     * @param string $where Klausa WHERE sebagai kondisi penghapusan (misal: "id = ?" atau "status = 'nonaktif'").
     * @param array $whereParams Nilai parameter untuk klausa WHERE (misal: [1] untuk "id = ?"). Kosong jika tidak ada parameter.
     * @return int Jumlah baris yang berhasil dihapus.
     * @throws \Exception Jika klausa WHERE kosong atau eksekusi gagal.
     */

    public function delete(string $table, string $where, array $whereParams = [])
    {
        if (empty($where)) {
            throw new \Exception("WHERE clause for delete cannot be empty.");
        }

        if (substr_count($where, '?') !== count($whereParams)) {
            throw new \Exception("Jumlah parameter tidak cocok dengan jumlah placeholder di WHERE.");
        }

        $sql = "DELETE FROM {$table} WHERE {$where}";
        $this->lastQuery = $sql; // Simpan query terakhir
        $this->query($sql);

        if (!empty($whereParams)) {
            // Pastikan param di-bind hanya jika ada ?
            $this->bind($this->getParamTypesAsArray(array_values($whereParams)));
        }

        $this->execute();

        return $this->rowCount();
    }


    /**
     * Helper: Mendapatkan array parameter dengan tipe untuk mysqli_stmt_bind_param.
     * Ini lebih fleksibel untuk `call_user_func_array`.
     *
     * @param array $data Array nilai-nilai yang akan di-bind.
     * @return array Array parameter dengan format ['tipe_string', nilai1, nilai2, ...].
     */
    private function getParamTypesAsArray(array $data)
    {
        $types = '';
        $values = [];
        foreach ($data as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b'; // blob atau tipe lainnya
            }
            $values[] = $value;
        }
        return array_merge([$types], $values);
    }

    public function lastQuery()
    {
        return $this->lastQuery; // Mengembalikan SQL terakhir yang dieksekusi
    }

    /**
     * Tutup koneksi saat objek dihancurkan.
     */
    public function __destruct()
    {
        if ($this->connection && !$this->connection->connect_error) {
            $this->connection->close();
        }
    }
}
