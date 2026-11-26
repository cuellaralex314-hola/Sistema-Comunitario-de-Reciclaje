<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Reciclaje - BarrioVerde</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    @php
        $kpis = $kpis ?? [
            'total_solicitudes' => 48,
            'completadas' => 34,
            'kg_estimados' => 412.5,
            'tasa_cumplimiento' => 70.8,
        ];

        $porMaterial = $porMaterial ?? [
            'labels' => ['Plástico (PET)', 'Papel y cartón', 'Vidrio', 'Metales / latas', 'Electrónicos', 'Aceite usado'],
            'valores' => [18, 14, 7, 5, 2, 2],
        ];

        $porEstado = $porEstado ?? [
            'labels' => ['Completadas', 'En camino', 'Asignadas', 'Pendientes', 'Canceladas'],
            'valores' => [34, 3, 4, 5, 2],
        ];

        $evolucionMensual = $evolucionMensual ?? [
            'labels' => ['Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov'],
            'valores' => [4, 7, 9, 11, 14, 19],
        ];
    @endphp

    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-lg font-bold text-emerald-600">
                BarrioVerde
            </a>
            <nav class="flex items-center gap-4 text-sm font-medium text-gray-600">
                <a href="{{ url('/admin/solicitudes') }}" class="hover:text-emerald-600 transition">
                    Solicitudes
                </a>
                <a href="{{ url('/estadisticas') }}" class="text-emerald-600 font-semibold">
                    Estadísticas
                </a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Encabezado --}}
        <div class="mb-8">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 mb-2">
                Administración · Reportes
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                Panel de Estadísticas de Reciclaje
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Visualizá el volumen de materiales recuperados, el estado de las solicitudes y la evolución mensual en Formosa.
            </p>
        </div>

        {{-- Tarjetas KPI superiores --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Solicitudes Totales</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $kpis['total_solicitudes'] }}</p>
                <p class="mt-1 text-xs text-gray-500">Registradas en la plataforma</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Retiros Completados</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $kpis['completadas'] }}</p>
                <p class="mt-1 text-xs text-emerald-700">Material entregado a puntos verdes</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Material Recuperado (Est.)</p>
                <p class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($kpis['kg_estimados'], 1, ',', '.') }} kg</p>
                <p class="mt-1 text-xs text-gray-500">Suma según volumen declarado</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Tasa de Cumplimiento</p>
                <p class="mt-2 text-3xl font-bold text-amber-600">{{ number_format($kpis['tasa_cumplimiento'], 1, ',', '.') }}%</p>
                <p class="mt-1 text-xs text-gray-500">Retiros finalizados sobre total</p>
            </div>
        </div>

        {{-- Fila principal de gráficos (Barras + Dona) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- Gráfico 1: Solicitudes por Material Reciclable --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Solicitudes por Tipo de Material</h2>
                        <p class="text-xs text-gray-500">Cantidad de retiros solicitados por categoría reciclable</p>
                    </div>
                </div>
                <div class="relative h-72">
                    <canvas id="chartMateriales" aria-label="Gráfico de barras de solicitudes por material reciclable" role="img"></canvas>
                </div>
            </div>

            {{-- Gráfico 2: Distribución por Estado --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Estado de Solicitudes</h2>
                    <p class="text-xs text-gray-500">Proporción operativa actual</p>
                </div>
                <div class="relative h-72 flex items-center justify-center">
                    <canvas id="chartEstados" aria-label="Gráfico circular de distribución por estado" role="img"></canvas>
                </div>
            </div>
        </div>

        {{-- Fila inferior: Evolución Mensual --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="mb-4">
                <h2 class="text-base font-semibold text-gray-900">Evolución Mensual de Retiros Completados</h2>
                <p class="text-xs text-gray-500">Crecimiento de recolecciones exitosas en los últimos meses</p>
            </div>
            <div class="relative h-64">
                <canvas id="chartEvolucion" aria-label="Gráfico de líneas de evolución mensual de retiros" role="img"></canvas>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const datosMateriales = @json($porMaterial);
        const datosEstados = @json($porEstado);
        const datosEvolucion = @json($evolucionMensual);

        new Chart(document.getElementById('chartMateriales'), {
            type: 'bar',
            data: {
                labels: datosMateriales.labels,
                datasets: [{
                    label: 'Solicitudes',
                    data: datosMateriales.valores,
                    backgroundColor: [
                        '#10b981',
                        '#34d399',
                        '#059669',
                        '#3b82f6',
                        '#f59e0b',
                        '#8b5cf6'
                    ],
                    borderRadius: 8,
                    maxBarThickness: 44
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: '#f3f4f6' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        new Chart(document.getElementById('chartEstados'), {
            type: 'doughnut',
            data: {
                labels: datosEstados.labels,
                datasets: [{
                    data: datosEstados.valores,
                    backgroundColor: [
                        '#10b981',
                        '#6366f1',
                        '#3b82f6',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 14,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('chartEvolucion'), {
            type: 'line',
            data: {
                labels: datosEvolucion.labels,
                datasets: [{
                    label: 'Retiros completados',
                    data: datosEvolucion.valores,
                    borderColor: '#059669',
                    backgroundColor: 'rgba(16, 185, 129, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#059669',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: '#f3f4f6' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
    </script>
</body>
</html>