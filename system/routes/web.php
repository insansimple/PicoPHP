<?php

use System\Core\Router;

$router = new Router();

$router->setAlias(['HomeController' => 'Home']);

$router->add('GET', '/', "Home/index");
$router->add('GET', '/home/:id', "Home/home");

return $router;
