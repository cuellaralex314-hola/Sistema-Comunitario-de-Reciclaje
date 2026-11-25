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

// Rutas privadas: solo para usuarios que iniciaron sesión. Un invitado que
// entre a una de estas páginas es enviado al login y, después de entrar,
// vuelve a la página que quería ver.
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

// Panel de administración (T05, T18): todas las rutas /admin/... pasan por
// 'auth' (invitado -> login) y 'rol:admin' (otro rol -> 403). Cada sección
// suma además su permiso con 'can:'. Nombres de ruta: admin.*
Route::middleware(['auth', 'rol:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', PanelController::class)->name('panel');

    // Usuarios y roles (T17)
    Route::middleware('can:usuarios.gestionar')->prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::patch('/{usuario}/rol', [UsuarioController::class, 'actualizarRol'])->name('rol');
        Route::patch('/{usuario}/estado', [UsuarioController::class, 'cambiarEstado'])->name('estado');
    });

    // Solicitudes de retiro (T33): asignar recolector y rechazar con motivo.
    Route::middleware('can:solicitudes.gestionar')->prefix('solicitudes')->name('solicitudes.')->group(function () {
        Route::patch('/{solicitud}/asignar', [SolicitudController::class, 'asignar'])->name('asignar');
        Route::patch('/{solicitud}/rechazar', [SolicitudController::class, 'rechazar'])->name('rechazar');
    });
});

// Recolector (T33): ve solo sus retiros asignados y los completa cargando
// los kg reales. Que la solicitud sea suya lo controla SolicitudRetiroPolicy.
Route::middleware(['auth', 'rol:recolector'])->prefix('recolector')->name('recolector.')->group(function () {
    Route::get('/retiros', [RetiroController::class, 'index'])->name('retiros.index');
    Route::patch('/retiros/{solicitud}/completar', [RetiroController::class, 'completar'])->name('retiros.completar');
});

// Guía de categorías (vista de Alex). La parte con datos reales la suma Fabián (T10).
Route::get('/categorias', function () {
    return view('categories.index');
})->name('categories.index');
