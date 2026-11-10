@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md border border-gray-100">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Iniciar Sesión</h2>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input type="password" id="password" name="password" required 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                <span class="ml-2 text-gray-600">Recuérdame</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-200">
                Ingresar
            </button>
        </div>
    </form>

    <div class="mt-4 text-center text-sm text-gray-600">
        ¿No tienes una cuenta? <a href="{{ route('register') }}" class="text-green-600 hover:underline font-medium">Regístrate aquí</a>
    </div>
</div>
@endsection