@extends('layouts.app')

@section('title', 'Nuevo Punto Verde — BarrioVerde')

@push('styles')
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossorigin=""
    />
    <style>
        #map-punto-verde {
            height: 360px;
            width: 100%;
            z-index: 1;
        }
    </style>
@endpush

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Registrar Punto Verde</h1>
            <p class="text-sm text-gray-500">Completá los datos del punto de recepción y seleccioná su ubicación en el mapa.</p>
        </div>
        <a
            href="{{ route('admin.puntos-verdes.index') }}"
            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
            Volver al listado
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Revisá los siguientes campos:</p>
            <ul class="mt-1 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('admin.puntos-verdes.store') }}"
        method="POST"
        class="grid grid-cols-1 gap-6 lg:grid-cols-2"
    >
        @csrf

        {{-- Columna izquierda: datos administrativos --}}
        <div class="space-y-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">
                    Nombre del Punto Verde <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    required
                    placeholder="Ej. EcoPunto Plaza San Martín"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="barrio" class="block text-sm font-medium text-gray-700">
                        Barrio / Sector <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="barrio"
                        name="barrio"
                        value="{{ old('barrio') }}"
                        required
                        placeholder="Ej. Barrio Virgen del Rosario"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                    />
                </div>

                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700">
                        Estado
                    </label>
                    <select
                        id="estado"
                        name="estado"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                    >
                        <option value="activo" @selected(old('estado', 'activo') === 'activo')>Activo</option>
                        <option value="mantenimiento" @selected(old('estado') === 'mantenimiento')>En mantenimiento</option>
                        <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="direccion" class="block text-sm font-medium text-gray-700">
                    Dirección de referencia <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="direccion"
                    name="direccion"
                    value="{{ old('direccion') }}"
                    required
                    placeholder="Ej. Av. 25 de Mayo 1200"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                />
            </div>

            <div>
                <label for="horario" class="block text-sm font-medium text-gray-700">
                    Horarios de atención
                </label>
                <input
                    type="text"
                    id="horario"
                    name="horario"
                    value="{{ old('horario') }}"
                    placeholder="Ej. Lun a Vie de 08:00 a 18:00"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                />
            </div>

            <div>
                <span class="block text-sm font-medium text-gray-700">Materiales recibidos</span>
                @php
                    $materialesDisponibles = ['Plástico', 'Papel y cartón', 'Vidrio', 'Metales', 'Electrónicos', 'Pilas'];
                    $seleccionados = old('materiales', []);
                @endphp
                <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @foreach ($materialesDisponibles as $material)
                        <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <input
                                type="checkbox"
                                name="materiales[]"
                                value="{{ $material }}"
                                @checked(in_array($material, $seleccionados))
                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                            />
                            <span>{{ $material }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="observaciones" class="block text-sm font-medium text-gray-700">
                    Observaciones
                </label>
                <textarea
                    id="observaciones"
                    name="observaciones"
                    rows="3"
                    placeholder="Indicaciones adicionales para vecinos o recolectores..."
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                >{{ old('observaciones') }}</textarea>
            </div>
        </div>

        {{-- Columna derecha: selector geográfico en mapa --}}
        <div class="flex flex-col justify-between space-y-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-800">Ubicación geográfica</h2>
                    <button
                        type="button"
                        id="btn-centrar-formosa"
                        class="text-xs font-medium text-emerald-700 hover:underline"
                    >
                        Centrar en Formosa
                    </button>
                </div>

                <p class="text-xs text-gray-500">
                    Hacé clic en el mapa o arrastrá el marcador para definir las coordenadas exactas.
                </p>

                <div id="map-punto-verde" class="rounded-lg border border-gray-300"></div>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div>
                        <label for="latitud" class="block text-xs font-medium text-gray-700">
                            Latitud <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="latitud"
                            name="latitud"
                            value="{{ old('latitud', '-26.1849') }}"
                            required
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 font-mono text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        />
                    </div>
                    <div>
                        <label for="longitud" class="block text-xs font-medium text-gray-700">
                            Longitud <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="longitud"
                            name="longitud"
                            value="{{ old('longitud', '-58.1731') }}"
                            required
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 font-mono text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        />
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                <a
                    href="{{ route('admin.puntos-verdes.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Cancelar
                </a>
                <button
                    type="submit"
                    class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    Guardar Punto Verde
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        crossorigin=""
    ></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const latInput = document.getElementById('latitud');
            const lngInput = document.getElementById('longitud');
            const btnCentrar = document.getElementById('btn-centrar-formosa');

            const defaultLat = -26.1849;
            const defaultLng = -58.1731;

            const initialLat = parseFloat(latInput.value) || defaultLat;
            const initialLng = parseFloat(lngInput.value) || defaultLng;

            const map = L.map('map-punto-verde').setView([initialLat, initialLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

            function actualizarCoordenadas(lat, lng, centrar = false) {
                const latFixed = Number(lat).toFixed(6);
                const lngFixed = Number(lng).toFixed(6);

                latInput.value = latFixed;
                lngInput.value = lngFixed;
                marker.setLatLng([latFixed, lngFixed]);

                if (centrar) {
                    map.setView([latFixed, lngFixed], map.getZoom());
                }
            }

            map.on('click', (e) => {
                actualizarCoordenadas(e.latlng.lat, e.latlng.lng, false);
            });

            marker.on('dragend', () => {
                const pos = marker.getLatLng();
                actualizarCoordenadas(pos.lat, pos.lng, false);
            });

            [latInput, lngInput].forEach((input) => {
                input.addEventListener('change', () => {
                    const lat = parseFloat(latInput.value);
                    const lng = parseFloat(lngInput.value);
                    if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
                        actualizarCoordenadas(lat, lng, true);
                    }
                });
            });

            btnCentrar?.addEventListener('click', () => {
                actualizarCoordenadas(defaultLat, defaultLng, true);
            });
        });
    </script>
@endpush