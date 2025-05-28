<?php

namespace System\Core;

// Mengimpor kelas Middleware dari namespace yang sama.
// Router akan berinteraksi dengan Middleware untuk memungkinkan chaining middleware ke rute.

use Exception;
use System\Core\Middleware;

/**
 * Kelas Router bertanggung jawab untuk mendefinisikan dan mencocokkan rute HTTP
 * dengan controller dan metode yang sesuai. Ini juga mendukung parameter rute
 * dan integrasi dengan sistem middleware.
 */
class Router
{
    // Properti privat untuk menyimpan semua rute yang terdaftar, dikelompokkan berdasarkan metode HTTP.
    // Contoh: ['get' => [['pattern' => '#^/users/(?P<id>[^/]+)$#', 'controller' => 'UserController/show']]]
    private $routes = [];

    // Properti privat untuk menyimpan alias controller.
    // Memungkinkan penggunaan nama pendek (alias) untuk nama kelas controller lengkap.
    // Contoh: ['Home' => 'HomeController']
    private $alias = [];

    // Properti untuk menyimpan instance dari kelas Middleware, memungkinkan chaining.
    private $middlewareInstance = null;

    /**
     * Mengembalikan semua rute yang telah terdaftar.
     *
     * @return array Daftar rute yang terdaftar.
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Menambahkan rute baru ke daftar rute.
     * Metode ini mengonversi pola URI menjadi ekspresi reguler
     * untuk pencocokan rute dan memungkinkan penanganan parameter rute.
     * Setelah menambahkan rute, metode ini mengembalikan instance Middleware
     * untuk memungkinkan chaining konfigurasi middleware.
     *
     * @param string $method Metode HTTP (misalnya, 'GET', 'POST', 'PUT', 'DELETE').
     * @param string $pattern Pola URI untuk rute (misalnya, '/users/:id', '/products').
     * @param string $controller String yang menunjukkan controller dan metode yang akan dipanggil (misalnya, 'UserController/index').
     * @return Middleware Instance dari kelas Middleware, untuk chaining.
     */
    public function add(string $method, string $pattern, string $controller)
    {
        // Mengubah metode HTTP menjadi huruf kecil untuk konsistensi.
        $method = strtolower($method);

        // Mengonversi placeholder parameter rute (misalnya, ':id') menjadi grup penangkapan regex.
        // '/:([\w]+)' akan cocok dengan '/:nama_parameter'
        // dan (?P<$1>[^/]+) akan membuat grup bernama ($1 merujuk pada 'nama_parameter')
        // yang menangkap segmen non-garis miring setelah garis miring.
        $pattern = rtrim($pattern, '/'); // Menghapus garis miring di akhir pola untuk konsistensi.
        $pattern = preg_replace('/\/:([\w]+)/', '/(?P<$1>[^/]+)', $pattern);

        // Menambahkan pembatas regex (^) dan ($) untuk memastikan pola cocok dengan seluruh URI,
        // mencegah pencocokan parsial.
        $pattern = "#^" . $pattern . "$#";

        // Menyimpan rute ke dalam array $routes, dikelompokkan berdasarkan metode HTTP.
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'controller' => $controller
        ];

        // Mendapatkan instance Middleware (pola Singleton) dan menyimpannya.
        // Ini memungkinkan kita untuk mengembalikan instance Middleware
        // sehingga Anda dapat langsung menambahkan middleware setelah mendefinisikan rute.
        // Contoh: $router->get('/users', 'UserController/index')->middleware('auth');
        $this->middlewareInstance = Middleware::getInstance();
        return $this->middlewareInstance;
    }

    /**
     * memungkinkan untuk mengisi multi method secara spesifik.
     *
     * @param array $methods Metode HTTP, misalnya ['get', 'post'].
     * @param string $pattern Pola URI.
     * @param string $controller Controller dan metode.
     * @return Middleware Instance dari kelas Middleware.
     */
    public function on(array $methods, $pattern, $controller)
    {
        $middleware = null;
        foreach ($methods as $m) {
            // Memastikan setiap metode yang diberikan adalah huruf kecil.
            $m = strtolower($m);
            // Memanggil metode add untuk setiap metode yang diberikan.
            $middleware = $this->add($m, $pattern, $controller);
        }
        return $middleware; // Mengembalikan instance middleware dari penambahan rute terakhir.
    }

    // --- Pintasan Metode HTTP-Spesifik ---
    // Metode-metode ini adalah pintasan untuk metode `add`
    // untuk rute GET, POST, PUT, DELETE, dan ANY,
    // membuat definisi rute lebih ringkas dan mudah dibaca.

    /**
     * Menambahkan rute GET.
     *
     * @param string $pattern Pola URI.
     * @param string $controller Controller dan metode.
     * @return Middleware Instance dari kelas Middleware.
     */
    public function get($pattern, $controller)
    {
        return $this->add('get', $pattern, $controller);
    }

    /**
     * Menambahkan rute POST.
     *
     * @param string $pattern Pola URI.
     * @param string $controller Controller dan metode.
     * @return Middleware Instance dari kelas Middleware.
     */
    public function post($pattern, $controller)
    {
        return $this->add('post', $pattern, $controller);
    }

    /**
     * Menambahkan rute PUT.
     *
     * @param string $pattern Pola URI.
     * @param string $controller Controller dan metode.
     * @return Middleware Instance dari kelas Middleware.
     */
    public function put($pattern, $controller)
    {
        return $this->add('put', $pattern, $controller);
    }

    /**
     * Menambahkan rute DELETE.
     *
     * @param string $pattern Pola URI.
     * @param string $controller Controller dan metode.
     * @return Middleware Instance dari kelas Middleware.
     */
    public function delete($pattern, $controller)
    {
        return $this->add('delete', $pattern, $controller);
    }

    /**
     * Menambahkan rute yang akan merespons pada metode HTTP apa pun (GET, POST, PUT, DELETE).
     *
     * @param string $pattern Pola URI.
     * @param string $controller Controller dan metode.
     * @return Middleware Instance dari kelas Middleware (instance terakhir yang ditambahkan).
     */
    public function any($pattern, $controller)
    {
        $middleware = null;
        // Melakukan iterasi pada metode HTTP umum dan menambahkan rute untuk masing-masing.
        foreach (['get', 'post', 'put', 'delete'] as $method) {
            $middleware = $this->add($method, $pattern, $controller);
        }
        return $middleware; // Mengembalikan instance middleware dari penambahan rute terakhir.
    }

    /**
     * Mengatur alias untuk nama kelas controller.
     * Ini memungkinkan penggunaan nama pendek untuk controller, misalnya 'Home' -> 'HomeController'.
     *
     * @param array $alias Array asosiatif alias => nama kelas controller.
     * Contoh: ['HomeController' => 'Home', 'UserController' => 'User']
     * @return void
     */
    public function setAlias(array $alias)
    {
        foreach ($alias as $key => $value) {
            $this->alias[$key] = $value;
        }
    }

    /**
     * Mencocokkan URI dan metode HTTP yang masuk dengan rute yang terdaftar
     * dan menjalankan controller dan metode yang sesuai.
     *
     * @param string $method Metode HTTP permintaan saat ini (misalnya, 'GET').
     * @param string $uri URI permintaan saat ini (misalnya, '/users/1').
     * @return void
     */
    public function dispatch($method, $uri)
    {
        // Mengubah metode HTTP yang masuk menjadi huruf kecil untuk pencocokan.
        $method = strtolower($method);
        $uri = rtrim($uri, '/'); // Menghapus garis miring di akhir URI untuk konsistensi.

        // Memeriksa apakah ada rute yang terdaftar untuk metode HTTP ini.
        if (!isset($this->routes[$method])) {
            // Jika tidak ada rute untuk metode ini, kirim respons 405 Method Not Allowed.
            http_response_code(405);
            throw new \Exception("Method tidak diizinkan untuk URI '$uri'");
            return;
        }

        // Melakukan iterasi pada rute yang terdaftar untuk metode HTTP ini.
        foreach ($this->routes[$method] as $route) {
            // Mencoba mencocokkan URI dengan pola regex rute.
            if (preg_match($route['pattern'], $uri, $matches)) {
                // Jika cocok, memisahkan string controller (misalnya, 'UserController/index')
                // menjadi nama kelas controller dan nama fungsi.
                list($controller, $function) = explode('/', $route['controller']);
                // Menyaring parameter rute yang ditangkap dari hasil preg_match.
                // 'is_string' digunakan untuk hanya mengambil parameter bernama (kunci string).
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // --- Resolusi Kelas Controller ---
                $resolvedClass = null;

                // Memeriksa apakah controller yang diberikan adalah alias.
                // Mencari nilai controller dalam array alias (misalnya, mencari 'HomeController' di array ['HomeController' => 'Home']).
                $aliasMatched = array_search($controller, $this->alias, true);
                if ($aliasMatched !== false) {
                    // Jika cocok dengan alias, gunakan alias tersebut untuk membangun nama kelas lengkap.
                    // Diasumsikan controller berada di namespace App\Controllers.
                    $resolvedClass = "App\\Controllers\\$aliasMatched";
                } else {
                    // Jika bukan alias, gunakan nama controller yang diberikan langsung.
                    $resolvedClass = "App\\Controllers\\$controller";
                }

                // Memeriksa apakah kelas controller yang ditentukan ada.
                if (class_exists($resolvedClass)) {
                    // Membuat instance baru dari controller.
                    $controllerInstance = new $resolvedClass;
                    // Memeriksa apakah metode yang ditentukan ada di instance controller.
                    if (method_exists($controllerInstance, $function)) {
                        // Memanggil metode controller dengan parameter rute yang diekstrak.
                        // call_user_func_array memungkinkan pemanggilan fungsi dengan array parameter.
                        call_user_func_array([$controllerInstance, $function], $params);
                        return; // Menghentikan eksekusi setelah rute cocok dan dijalankan.
                    }
                }

                // Jika kelas atau metode controller tidak ditemukan (setelah cocok dengan pola rute).
                http_response_code(500); // Kode status 500 Internal Server Error.
                throw new \Exception("Controller atau method tidak ditemukan found");
                return;
            }
        }

        // --- Penanganan Rute Tidak Ditemukan (404) ---
        // Jika tidak ada rute yang cocok setelah memeriksa semua rute.
        http_response_code(404); // Kode status 404 Not Found.
        // Memeriksa mode debug aplikasi (Diasumsikan ada fungsi `config('debug')`).
        if (function_exists('config') && config('debug') === true) {
            // Jika mode debug aktif, lempar pengecualian dengan detail URI.
            throw new \Exception("404 Not Found: URL yang direquest '$uri' tidak ada pada server ini.");
        } else {
            // Jika mode debug tidak aktif, sertakan halaman kesalahan 404 kustom.
            // Jalur disesuaikan agar sesuai dengan struktur kerangka kerja.
            require_once __DIR__ . '/../error/404.php';
        }
    }
}
