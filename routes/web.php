<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/publicaciones/crear', function () {
    return view('publications.create');
})->name('publications.create');