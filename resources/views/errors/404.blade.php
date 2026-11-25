@extends('layouts.app')

@section('title', 'Página no encontrada')

@section('content')
<div class="max-w-md mx-auto text-center py-16 space-y-4">
    <div class="text-6xl font-bold text-green-600">404</div>
    <h1 class="text-2xl font-bold text-gray-800">Página no encontrada</h1>
    <p class="text-gray-600 text-sm">
        Lo sentimos, la página que estás buscando no existe o ha sido movida dentro del Sistema Comunitario de Reciclaje.
    </p>
    <div class="pt-4">
        <a href="{{ route('home') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-2 rounded-lg transition shadow">
            Volver al Inicio
        </a>
    </div>
</div>
@endsection