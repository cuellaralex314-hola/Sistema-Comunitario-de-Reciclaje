<?php

use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\Admin\SolicitudController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\Recolector\RetiroController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegistroController::class, 'create'])->name('register');
    Route::post('/register', [RegistroController::class, 'store'])->name('register.store');

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/publicaciones/crear', function () {
        return view('publications.create');
    })->name('publications.create');

    Route::get('/mis-publicaciones', function () {
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
});

Route::middleware(['auth', 'rol:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', PanelController::class)->name('panel');

    Route::middleware('can:usuarios.gestionar')->prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::patch('/{usuario}/rol', [UsuarioController::class, 'actualizarRol'])->name('rol');
        Route::patch('/{usuario}/estado', [UsuarioController::class, 'cambiarEstado'])->name('estado');
    });

    Route::middleware('can:solicitudes.gestionar')->prefix('solicitudes')->name('solicitudes.')->group(function () {
        Route::patch('/{solicitud}/asignar', [SolicitudController::class, 'asignar'])->name('asignar');
        Route::patch('/{solicitud}/rechazar', [SolicitudController::class, 'rechazar'])->name('rechazar');
    });

    Route::view('/puntos-verdes/crear', 'admin.puntos-verdes.create')->name('puntos-verdes.create');
    Route::redirect('/puntos-verdes', '/')->name('puntos-verdes.index');
    Route::post('/puntos-verdes', fn () => redirect('/'))->name('puntos-verdes.store');
});

Route::middleware(['auth', 'rol:recolector'])->prefix('recolector')->name('recolector.')->group(function () {
    Route::get('/retiros', [RetiroController::class, 'index'])->name('retiros.index');
    Route::patch('/retiros/{solicitud}/completar', [RetiroController::class, 'completar'])->name('retiros.completar');
});

Route::get('/categorias', function () {
    return view('categories.index');
})->name('categories.index');

Route::get('/puntos-verdes', function () {
    return view('puntos-verdes.index');
})->name('puntos-verdes.index');