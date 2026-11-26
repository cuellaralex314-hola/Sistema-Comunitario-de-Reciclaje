<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio del Vecino - BarrioVerde</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    @php
        $resumen = $resumen ?? [
            'activas' => 2,
            'completadas' => 5,
            'puntos_verdes' => 3,
        ];

        $ultimasSolicitudes = $ultimasSolicitudes ?? [
            [
                'codigo' => 'SOL-0104',
                'fecha' => '21/11/2026',
                'turno' => 'Mañana (08:00 a 12:00)',
                'estado' => 'Pendiente',
                'materiales' => ['Plástico (PET / envases)', 'Papel y cartón', 'Vidrio'],
            ],
            [
                'codigo' => 'SOL-0096',
                'fecha' => '12/11/2026',
                'turno' => 'Mañana (08:00 a 12:00)',
                'estado' => 'Completada',
                'materiales' => ['Papel y cartón', 'Vidrio'],
            ],
        ];
    @endphp

    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/vecino') }}" class="text-lg font-bold text-emerald-600">
                BarrioVerde
            </a>
            <nav class="flex items-center gap-4 text-sm font-medium text-gray-600">
                <a href="{{ url('/vecino') }}" class="text-emerald-600 font-semibold">
                    Inicio
                </a>
                <a href="{{ url('/solicitudes/nueva') }}" class="hover:text-emerald-600 transition">
                    Nueva solicitud
                </a>
                <a href="{{ url('/mis-solicitudes') }}" class="hover:text-emerald-600 transition">
                    Mis solicitudes
                </a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Bienvenida y acción principal --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-sm mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 mb-3">
                        Portal del Vecino · Formosa
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        ¡Hola! Coordiná el retiro de tus reciclables en casa
                    </h1>
                    <p class="mt-2 text-sm sm:text-base text-gray-600 max-w-2xl">
                        Solicitá retiros a domicilio, consultá el estado de tus entregas y conocé qué materiales podés separar para cuidar tu barrio.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="{{ url('/solicitudes/nueva') }}"
                       class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition shadow-sm">
                        + Nueva solicitud de retiro
                    </a>
                    <a href="{{ url('/mis-solicitudes') }}"
                       class="inline-flex items-center justify-center px-4 py-3 rounded-xl border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Ver mis solicitudes
                    </a>
                </div>
            </div>
        </div>

        {{-- Resumen rápido --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Solicitudes Activas</p>
                <p class="mt-2 text-3xl font-bold text-amber-600">{{ $resumen['activas'] }}</p>
                <p class="mt-1 text-xs text-gray-500">Pendientes, asignadas o en camino</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Retiros Completados</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $resumen['completadas'] }}</p>
                <p class="mt-1 text-xs text-emerald-700">Aportes registrados en el programa</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Puntos de Recepción</p>
                <p class="mt-2 text-3xl font-bold text-blue-600">{{ $resumen['puntos_verdes'] }}</p>
                <p class="mt-1 text-xs text-gray-500">Espacios habilitados en la ciudad</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Últimas solicitudes --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Tus últimas solicitudes</h2>
                        <p class="text-xs text-gray-500">Seguimiento rápido de tus retiros recientes</p>
                    </div>
                    <a href="{{ url('/mis-solicitudes') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                        Ver todas →
                    </a>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach ($ultimasSolicitudes as $sol)
                        <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900">{{ $sol['codigo'] }}</span>
                                    <span class="text-xs text-gray-500">· {{ $sol['fecha'] }} · {{ $sol['turno'] }}</span>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach ($sol['materiales'] as $mat)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $mat }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <span class="self-start sm:self-center inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                {{ $sol['estado'] === 'Completada' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $sol['estado'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Guía rápida de materiales --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">Materiales reciclables</h2>
                <p class="text-xs text-gray-500 mt-0.5 mb-4">
                    Separalos limpios y secos antes del retiro:
                </p>

                <ul class="space-y-2.5 text-sm text-gray-700">
                    <li class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Plástico (PET / envases)
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Papel y cartón
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Vidrio
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Metales / latas
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Electrónicos pequeños
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Aceite vegetal usado
                    </li>
                </ul>
            </div>
        </div>
    </main>
</body>
</html>