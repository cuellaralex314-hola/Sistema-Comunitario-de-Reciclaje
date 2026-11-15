@extends('layouts.app')

@section('title', 'Registro de Vecino')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md border border-gray-100">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Crear Cuenta de Vecino</h2>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
        </div>

        <div>
            <label for="lastname" class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
            <input type="text" id="lastname" name="lastname" value="{{ old('lastname') }}" required 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
        </div>

        <div>
            <label for="neighborhood" class="block text-sm font-medium text-gray-700 mb-1">Barrio</label>
            <input type="text" id="neighborhood" name="neighborhood" value="{{ old('neighborhood') }}" required 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input type="password" id="password" name="password" required 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-200">
                Registrarse
            </button>
        </div>
    </form>

    <div class="mt-4 text-center text-sm text-gray-600">
        ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-green-600 hover:underline font-medium">Inicia sesión</a>
    </div>
</div>
@endsection