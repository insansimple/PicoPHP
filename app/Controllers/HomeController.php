<?php

namespace App\Controllers;

use System\Core\Controller;
use Closure;

class HomeController extends Controller
{
    public function index()
    {
        echo "Welcome to the Home Page!";
    }

    public function home($id)
    {
        echo $id;
    }
}
