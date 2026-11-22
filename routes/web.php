<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/solicitudes/nueva', 'solicitudes.create')
    ->name('solicitudes.create');

Route::view('/mis-solicitudes', 'solicitudes.index')
    ->name('solicitudes.index');

Route::patch('/solicitudes/{id}/cancelar', fn () => back()->with('status', 'La solicitud fue cancelada.'))
    ->name('solicitudes.cancelar');

Route::post('/solicitudes', fn () => back()->with('status', 'Solicitud registrada en la vista de prueba.'))
    ->name('solicitudes.store');