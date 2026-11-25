<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BarrioVerde') }} - @yield('title', 'Inicio')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-800 flex flex-col min-h-screen">

    <header class="bg-white shadow">
        @include('layouts.navigation')
    </header>

    <main class="flex-grow container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('status'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 py-4 text-center text-sm text-gray-600">
        <div class="container mx-auto px-4">
            <p>&copy; {{ date('Y') }} Sistema Comunitario de Gestión y Reciclaje de Residuos. Todos los derechos reservados.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>