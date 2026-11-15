<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::match(['get', 'post'], '/login', function () {
    if (request()->isMethod('post')) {
        return 'Datos de inicio de sesión recibidos (en desarrollo)';
    }
    return view('auth.login');
})->name('login');

Route::match(['get', 'post'], '/register', function () {
    if (request()->isMethod('post')) {
        return 'Datos de registro recibidos correctamente (en desarrollo)';
    }
    return view('auth.register');
})->name('register');

Route::get('/categorias', function () {
    return view('categories.index');
})->name('categories.index');
