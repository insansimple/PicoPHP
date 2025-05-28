<?php

namespace App\Controllers;

use System\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $this->middleware('auth');
        echo "Welcome to the Home Page!";
    }

    public function home($id)
    {
        echo $id;
    }
}
