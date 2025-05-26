<?php

namespace Core;

use Core\Router;

class App
{
    protected Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->loadHelpers();
    }

    public function run()
    {
        // Load routes (bisa dari file route eksternal)
        $this->loadRoutes();

        // Dispatch request ke router
        $this->router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    }

    protected function loadRoutes()
    {
        // Inject $app ke global supaya bisa diakses dari helper
        global $app;
        $app = $this;

        // Load helper route
        require_once __DIR__ . '/../library/helpers/route.php';

        // Load routes
        require __DIR__ . '/../routes/web.php';
    }


    // Untuk menambahkan route dari luar, misal di routes/web.php
    public function getRouter(): Router
    {
        return $this->router;
    }

    protected function loadHelpers()
    {
        foreach (glob(__DIR__ . '/../library/helpers/*.php') as $file) {
            require_once $file;
        }
    }
}
