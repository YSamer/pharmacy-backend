<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $test = 'Hello, world!';
    return Inertia::render('Test', compact( 'test'));
});
