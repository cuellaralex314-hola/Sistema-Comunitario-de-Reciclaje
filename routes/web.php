<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/solicitudes/nueva', 'solicitudes.create')
    ->name('solicitudes.create');

Route::redirect('/mis-solicitudes', '/')
    ->name('solicitudes.index');

Route::post('/solicitudes', fn () => back()->with('status', 'Solicitud registrada en la vista de prueba.'))
    ->name('solicitudes.store');