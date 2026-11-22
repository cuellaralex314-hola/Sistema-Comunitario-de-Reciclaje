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

Route::view('/admin/solicitudes', 'admin.solicitudes.index')
    ->name('admin.solicitudes.index');

Route::patch('/admin/solicitudes/{id}/asignar', fn () => back()->with('status', 'Solicitud actualizada correctamente.'))
    ->name('admin.solicitudes.asignar');

Route::view('/recolector/solicitudes', 'recolector.solicitudes.index')
    ->name('recolector.solicitudes.index');

Route::patch('/recolector/solicitudes/{id}/estado', fn () => back()->with('status', 'Estado del retiro actualizado.'))
    ->name('recolector.solicitudes.estado');

Route::patch('/notificaciones/leer-todas', fn () => back()->with('status', 'Todas las notificaciones fueron marcadas como leídas.'))
    ->name('notificaciones.leer-todas');