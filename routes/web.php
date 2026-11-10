<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    return 'Pantalla de Login (en desarrollo)';
})->name('login');

Route::match(['get', 'post'], '/register', function () {
    if (request()->isMethod('post')) {
        return 'Datos de registro recibidos correctamente (en desarrollo)';
    }
    return view('auth.register');
})->name('register');
