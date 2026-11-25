@extends('layouts.app')

@section('title', 'Acceso denegado')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md border border-gray-100 text-center">
    <p class="text-5xl font-bold text-red-600 mb-2">403</p>
    <h2 class="text-2xl font-bold text-gray-800 mb-3">Acceso denegado</h2>
    <p class="text-gray-600 mb-6">
        {{ $exception->getMessage() ?: 'No tenés permiso para acceder a esta sección.' }}
    </p>
    <a href="{{ route('home') }}"
       class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-200">
        Volver al inicio
    </a>
</div>
@endsection
