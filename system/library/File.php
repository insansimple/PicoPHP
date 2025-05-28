<?php

namespace System\Library;

class File
{
    /**
     * Membaca isi file dan mengembalikannya sebagai string.
     *
     * @param string $file_path Path ke file yang akan dibaca.
     * @return string Isi dari file.
     */
    public static function read($file_path)
    {
        if (!file_exists($file_path)) {
            throw new \Exception("File tidak ditemukan: " . $file_path);
        }
        return file_get_contents($file_path);
    }

    /**
     * Menulis data ke file.
     *
     * @param string $file_path Path ke file yang akan ditulis.
     * @param string $data Data yang akan ditulis ke file.
     * @return void
     */
    public static function write($file_path, $data)
    {
        file_put_contents($file_path, $data);
    }

    /**
     * Menghapus file.
     *
     * @param string $file_path Path ke file yang akan dihapus.
     * @return void
     */
    public static function delete($file_path)
    {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    public static function download($file_path, $download_name = null)
    {
        if (!file_exists($file_path)) {
            throw new \Exception("File tidak ditemukan: " . $file_path);
        }

        if (is_null($download_name)) {
            $download_name = basename($file_path);
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $download_name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
    }
}
