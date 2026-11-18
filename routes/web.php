<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/publicaciones/crear', function () {
    return view('publications.create');
})->name('publications.create');

Route::get('/mis-publicaciones', function () {
    // Datos de prueba temporales para simular las publicaciones del usuario
    $publicaciones = [
        [
            'id' => 1,
            'title' => 'Lote de botellas PET transparentes limpias',
            'category' => 'Plásticos',
            'quantity' => '10 kg',
            'status' => 'Activo',
            'status_class' => 'bg-green-100 text-green-800',
            'created_at' => '12 de Noviembre, 2026',
            'address' => 'Barrio San Martín, Av. 25 de Mayo 1200'
        ],
        [
            'id' => 2,
            'title' => 'Cajas de cartón corrugado desarmadas',
            'category' => 'Papel y Cartón',
            'quantity' => '5 kg',
            'status' => 'Retirado',
            'status_class' => 'bg-gray-100 text-gray-800',
            'created_at' => '05 de Noviembre, 2026',
            'address' => 'Barrio Centro, San Martín 450'
        ]
    ];

    return view('publications.index', compact('publicaciones'));
})->name('publications.index');