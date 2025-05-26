<?php

namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        view('home', ['title' => 'Welcome to PicoPHP']);
    }
}
