<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    return 'Pantalla de Login (en desarrollo)';
})->name('login');

Route::get('/register', function () {
    return 'Pantalla de Registro (en desarrollo)';
})->name('register');
