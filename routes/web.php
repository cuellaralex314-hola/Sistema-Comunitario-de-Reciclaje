<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/estadisticas', function () {
    return view('admin.estadisticas');
})->name('estadisticas');

Route::get('/admin/estadisticas', function () {
    return view('admin.estadisticas');
})->name('admin.estadisticas');

Route::get('/vecino', function () {
    return view('vecino.inicio');
})->name('vecino.inicio');