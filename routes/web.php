<?php

use App\Controllers\HomeController;

get('/', [new HomeController(), 'index']);
get('/about', function () {
    echo "About page";
});
post('/contact', function () {
    echo "Handle contact form";
});

on(['GET', 'POST'], '/submit', [new HomeController(), 'submit']);
