<?php

namespace System\Library;

class Upload
{
    private static $instance = null;
    private $file = null;
    /**
     * Mendapatkan instance dari kelas Upload.
     *
     * @return Upload Instance dari kelas Upload.
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * Mengatur file yang akan diupload.
     *
     * @param array $file Array yang berisi informasi file yang diupload.
     * @return void
     */
    public function setFile(array $file)
    {
        // jangan jalankan ketika dipanggil dari cli
        if (php_sapi_name() !== 'cli') {
            if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                throw new \Exception("File upload error: No file uploaded or invalid file.");
            }
        }
        $this->file = $file;
    }

    public function getFile()
    {
        if ($this->file === null) {
            throw new \Exception("No file has been set for upload.");
        }
        return $this->file;
    }

    public function getFileName()
    {
        if ($this->file === null) {
            throw new \Exception("No file has been set for upload.");
        }
        return pathinfo($this->file['name'], PATHINFO_FILENAME);
    }

    //return name with extension
    public function getFullName()
    {
        if ($this->file === null) {
            throw new \Exception("No file has been set for upload.");
        }
        return $this->file['name'];
    }

    public function getFileSize()
    {
        if ($this->file === null) {
            throw new \Exception("No file has been set for upload.");
        }
        return $this->file['size'];
    }

    public function getFileType()
    {
        if ($this->file === null) {
            throw new \Exception("No file has been set for upload.");
        }
        return $this->file['type'];
    }

    public function store($destination, $filename = null)
    {
        if ($this->file === null) {
            throw new \Exception("No file has been set for upload.");
        }

        if (!is_dir($destination)) {
            throw new \Exception("Destination directory does not exist: " . $destination);
        }

        if (!is_writable($destination)) {
            throw new \Exception("Destination directory is not writable: " . $destination);
        }

        if (is_null($filename)) {
            $filename = $this->getFullName();
        }

        $targetPath = rtrim($destination, '/') . '/' . $filename;

        if (!php_sapi_name() === 'cli') {
            if (!move_uploaded_file($this->file['tmp_name'], $targetPath)) {
                throw new \Exception("Failed to move uploaded file to destination: " . $targetPath);
            }
        }

        return $targetPath;
    }
}
