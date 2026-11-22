@extends('layouts.app')

@section('title', 'Mis Solicitudes de Retiro — BarrioVerde')

@section('content')
@php
    $solicitudesListado = $solicitudes ?? collect([
        (object) [
            'id' => 104,
            'codigo' => 'SOL-0104',
            'fecha' => '21/11/2026',
            'materiales' => ['Plástico (PET / envases)', 'Papel y cartón', 'Vidrio'],
            'peso_aproximado' => 'Mediano (5 a 15 kg)',
            'franja_horaria' => 'Mañana (08:00 a 12:00)',
            'barrio' => 'Barrio San Martín',
            'direccion' => 'Av. 25 de Mayo 845',
            'estado' => 'pendiente',
            'recolector' => null,
            'observaciones' => 'Las bolsas están separadas por material en la entrada.',
        ],
        (object) [
            'id' => 101,
            'codigo' => 'SOL-0101',
            'fecha' => '18/11/2026',
            'materiales' => ['Electrónicos pequeños', 'Metales / latas'],
            'peso_aproximado' => 'Liviano (hasta 5 kg)',
            'franja_horaria' => 'Tarde (13:00 a 17:00)',
            'barrio' => 'Barrio San Martín',
            'direccion' => 'Av. 25 de Mayo 845',
            'estado' => 'asignada',
            'recolector' => 'Carlos Gómez',
            'observaciones' => 'Tocar timbre negro.',
        ],
        (object) [
            'id' => 96,
            'codigo' => 'SOL-0096',
            'fecha' => '12/11/2026',
            'materiales' => ['Papel y cartón'],
            'peso_aproximado' => 'Grande (más de 15 kg)',
            'franja_horaria' => 'Mañana (08:00 a 12:00)',
            'barrio' => 'Barrio San Martín',
            'direccion' => 'Av. 25 de Mayo 845',
            'estado' => 'completada',
            'recolector' => 'Lucía Fernández',
            'observaciones' => 'Cajas plegadas y atadas.',
        ],
    ]);

    $estadoFiltro = request('estado', 'todas');

    if ($estadoFiltro !== 'todas') {
        $solicitudesListado = $solicitudesListado->filter(
            fn ($item) => $item->estado === $estadoFiltro
        );
    }

    $estilosEstado = [
        'pendiente'  => ['label' => 'Pendiente',  'badge' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'asignada'   => ['label' => 'Asignada',   'badge' => 'bg-blue-50 text-blue-700 border-blue-200'],
        'en_camino'  => ['label' => 'En camino',  'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
        'completada' => ['label' => 'Completada', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'cancelada'  => ['label' => 'Cancelada',  'badge' => 'bg-gray-100 text-gray-600 border-gray-200'],
    ];
@endphp

<div class="mx-auto max-w-6xl px-4 py-8">
    {{-- Encabezado --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                Solicitudes y retiros
            </span>
            <h1 class="mt-2 text-2xl font-bold text-gray-800">Mis Solicitudes</h1>
            <p class="text-sm text-gray-500">
                Seguí el estado de tus pedidos de recolección y revisá los turnos asignados.
            </p>
        </div>

        <a
            href="{{ route('solicitudes.create') }}"
            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700"
        >
            <span>+ Nueva solicitud de retiro</span>
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    {{-- Filtros por estado --}}
    <div class="mb-6 flex flex-wrap items-center gap-2">
        @php
            $filtros = [
                'todas'      => 'Todas',
                'pendiente'  => 'Pendientes',
                'asignada'   => 'Asignadas',
                'en_camino'  => 'En camino',
                'completada' => 'Completadas',
                'cancelada'  => 'Canceladas',
            ];
        @endphp

        @foreach ($filtros as $clave => $texto)
            <a
                href="{{ route('solicitudes.index', $clave === 'todas' ? [] : ['estado' => $clave]) }}"
                class="rounded-full border px-3.5 py-1.5 text-xs font-medium transition {{ $estadoFiltro === $clave
                    ? 'border-emerald-600 bg-emerald-600 text-white'
                    : 'border-gray-200 bg-white text-gray-600 hover:border-emerald-300 hover:text-emerald-700' }}"
            >
                {{ $texto }}
            </a>
        @endforeach
    </div>

    {{-- Listado de solicitudes --}}
    @if ($solicitudesListado->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 bg-white p-12 text-center">
            <h2 class="text-base font-semibold text-gray-800">No tenés solicitudes en esta categoría</h2>
            <p class="mt-1 text-sm text-gray-500">
                Creá una nueva solicitud de retiro para coordinar la recolección de tus reciclables.
            </p>
            <a
                href="{{ route('solicitudes.create') }}"
                class="mt-4 inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
            >
                Solicitar retiro
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($solicitudesListado as $solicitud)
                @php
                    $infoEstado = $estilosEstado[$solicitud->estado] ?? $estilosEstado['pendiente'];
                @endphp

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-emerald-200">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-mono text-sm font-bold text-gray-800">
                                    {{ $solicitud->codigo ?? 'SOL-' . str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $infoEstado['badge'] }}">
                                    {{ $infoEstado['label'] }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    Solicitado el {{ $solicitud->fecha }}
                                </span>
                            </div>

                            <p class="text-sm font-medium text-gray-800">
                                {{ $solicitud->direccion }} · <span class="text-gray-500">{{ $solicitud->barrio }}</span>
                            </p>
                        </div>

                        <div class="text-right text-xs text-gray-500">
                            <p><span class="font-medium text-gray-700">Franja:</span> {{ $solicitud->franja_horaria }}</p>
                            <p class="mt-0.5"><span class="font-medium text-gray-700">Volumen:</span> {{ $solicitud->peso_aproximado }}</p>
                        </div>
                    </div>

                    {{-- Materiales --}}
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @foreach ((array) $solicitud->materiales as $mat)
                            <span class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                {{ $mat }}
                            </span>
                        @endforeach
                    </div>

                    {{-- Pie de tarjeta: recolector, observaciones y acciones --}}
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-3 text-xs">
                        <div class="space-y-0.5 text-gray-500">
                            @if (!empty($solicitud->recolector))
                                <p>
                                    <span class="font-medium text-gray-700">Recolector asignado:</span>
                                    {{ $solicitud->recolector }}
                                </p>
                            @else
                                <p class="text-amber-700">Aguardando asignación de recolector</p>
                            @endif

                            @if (!empty($solicitud->observaciones))
                                <p class="text-gray-400">{{ $solicitud->observaciones }}</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($solicitud->estado === 'pendiente')
                                <form action="{{ route('solicitudes.cancelar', $solicitud->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 font-medium text-red-700 hover:bg-red-100"
                                    >
                                        Cancelar solicitud
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection