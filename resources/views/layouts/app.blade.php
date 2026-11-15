<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarrioVerde</title>
    <!-- Tailwind CSS CDN o Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    
    <!-- Navbar o cabecera si la tienes -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-xl font-bold text-green-600">🌿 BarrioVerde</a>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main>
        @yield('content')
    </main>

</body>
</html>