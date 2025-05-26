<?php

namespace Core;

class Controller
{
    public function render_view($view, $data = [])
    {
        $viewFile = __DIR__ . '/../app/Views/' . $view . '.php';

        if (file_exists($viewFile)) {
            extract($data); // Ubah array jadi variabel lokal
            ob_start();
            require $viewFile;
            return ob_get_clean();
        } else {
            http_response_code(500);
            echo "View file '$view.php' not found.";
        }
    }

    public function load_helper($helperName)
    {
        $helperFile = __DIR__ . '/../library/helpers/' . $helperName . '.php';

        if (file_exists($helperFile)) {
            require_once $helperFile;
        } else {
            throw new \Exception("Helper file '$helperName.php' not found in helpers directory.");
        }
    }

    public function load_library($name)
    {
        $path = __DIR__ . "/../library/{$name}.php";

        if (file_exists($path)) {
            require_once $path;

            if (class_exists($name)) {
                return new $name();
            } else {
                throw new \Exception("Library class '$name' not found.");
            }
        } else {
            throw new \Exception("Library file '$name.php' not found.");
        }
    }


    public function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    // Fungsi lain bisa ditambahkan di sini, seperti middleware, JSON response, dll.
}
