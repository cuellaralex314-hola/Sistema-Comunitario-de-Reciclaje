@extends('layouts.app')

@section('title', 'Solicitar Retiro de Reciclables — BarrioVerde')

@push('styles')
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossorigin=""
    />
    <style>
        #map-solicitud-retiro {
            min-height: 280px;
            height: 280px;
            width: 100%;
            z-index: 1;
        }
    </style>
@endpush

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                Solicitudes y retiros
            </span>
            <h1 class="mt-2 text-2xl font-bold text-gray-800">Nueva Solicitud de Retiro</h1>
            <p class="text-sm text-gray-500">
                Indicá qué materiales reciclables querés entregar, tu dirección y tu disponibilidad horaria.
            </p>
        </div>

        <a
            href="{{ route('solicitudes.index') }}"
            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
            Ver mis solicitudes
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Revisá los campos obligatorios antes de enviar:</p>
            <ul class="mt-1 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('solicitudes.store') }}"
        method="POST"
        class="grid grid-cols-1 gap-6 lg:grid-cols-3"
    >
        @csrf

        {{-- Columna principal (2/3): materiales, dirección y horario --}}
        <div class="space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
            <div>
                <span class="block text-sm font-medium text-gray-700">
                    Materiales a retirar <span class="text-red-500">*</span>
                </span>
                <p class="text-xs text-gray-500">Seleccioná uno o más tipos de residuos limpios y secos.</p>

                @php
                    $materiales = ['Plástico (PET / envases)', 'Papel y cartón', 'Vidrio', 'Metales / latas', 'Electrónicos pequeños', 'Aceite vegetal usado'];
                    $seleccionados = old('materiales', []);
                @endphp

                <div class="mt-3 grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                    @foreach ($materiales as $material)
                        <label class="flex cursor-pointer items-center gap-2.5 rounded-lg border border-gray-200 px-3.5 py-2.5 text-sm text-gray-700 transition hover:border-emerald-300 hover:bg-emerald-50/40">
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

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="peso_aproximado" class="block text-sm font-medium text-gray-700">
                        Volumen / peso estimado <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="peso_aproximado"
                        name="peso_aproximado"
                        required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                    >
                        <option value="">Seleccioná una opción</option>
                        <option value="liviano_hasta_5kg" @selected(old('peso_aproximado') === 'liviano_hasta_5kg')>Liviano (1 a 2 bolsas / hasta 5 kg)</option>
                        <option value="mediano_5_15kg" @selected(old('peso_aproximado') === 'mediano_5_15kg')>Mediano (3 a 5 bolsas / 5 a 15 kg)</option>
                        <option value="grande_mas_15kg" @selected(old('peso_aproximado') === 'grande_mas_15kg')>Grande / volumen alto (más de 15 kg)</option>
                    </select>
                </div>

                <div>
                    <label for="franja_horaria" class="block text-sm font-medium text-gray-700">
                        Franja horaria preferida <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="franja_horaria"
                        name="franja_horaria"
                        required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                    >
                        <option value="">Seleccioná un turno</option>
                        <option value="manana_08_12" @selected(old('franja_horaria') === 'manana_08_12')>Mañana (08:00 a 12:00)</option>
                        <option value="tarde_13_17" @selected(old('franja_horaria') === 'tarde_13_17')>Siesta / Tarde (13:00 a 17:00)</option>
                        <option value="noche_17_20" @selected(old('franja_horaria') === 'noche_17_20')>Última hora (17:00 a 20:00)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="barrio" class="block text-sm font-medium text-gray-700">
                        Barrio <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="barrio"
                        name="barrio"
                        value="{{ old('barrio') }}"
                        required
                        placeholder="Ej. Barrio San Martín"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                    />
                </div>

                <div>
                    <label for="telefono_contacto" class="block text-sm font-medium text-gray-700">
                        Teléfono de contacto
                    </label>
                    <input
                        type="text"
                        id="telefono_contacto"
                        name="telefono_contacto"
                        value="{{ old('telefono_contacto') }}"
                        placeholder="Ej. 3704-000000"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                    />
                </div>
            </div>

            <div>
                <label for="direccion" class="block text-sm font-medium text-gray-700">
                    Dirección de retiro <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="direccion"
                    name="direccion"
                    value="{{ old('direccion') }}"
                    required
                    placeholder="Calle, altura, manzana/casa o referencia"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                />
            </div>

            <div>
                <label for="observaciones" class="block text-sm font-medium text-gray-700">
                    Observaciones para el recolector
                </label>
                <textarea
                    id="observaciones"
                    name="observaciones"
                    rows="3"
                    placeholder="Ej. Tocar timbre en reja verde, bolsas dejadas en el porche..."
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                >{{ old('observaciones') }}</textarea>
            </div>
        </div>

        {{-- Columna lateral (1/3): ubicación en mapa y envío --}}
        <div class="flex flex-col justify-between space-y-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-800">Referencia en mapa</h2>
                    <button
                        type="button"
                        id="btn-centrar-formosa"
                        class="text-xs font-medium text-emerald-700 hover:underline"
                    >
                        Centrar en Formosa
                    </button>
                </div>

                <p class="text-xs text-gray-500">
                    Opcional: ajustá el pin para indicar dónde se retiran los materiales.
                </p>

                <div id="map-solicitud-retiro" class="rounded-lg border border-gray-300"></div>

                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div>
                        <label for="latitud" class="block text-xs font-medium text-gray-700">Latitud</label>
                        <input
                            type="text"
                            id="latitud"
                            name="latitud"
                            value="{{ old('latitud', '-26.184900') }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 font-mono text-xs focus:border-emerald-500 focus:ring-emerald-500"
                        />
                    </div>
                    <div>
                        <label for="longitud" class="block text-xs font-medium text-gray-700">Longitud</label>
                        <input
                            type="text"
                            id="longitud"
                            name="longitud"
                            value="{{ old('longitud', '-58.173100') }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 font-mono text-xs focus:border-emerald-500 focus:ring-emerald-500"
                        />
                    </div>
                </div>
            </div>

            <div class="space-y-2 border-t border-gray-100 pt-4">
                <button
                    type="submit"
                    class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    Enviar solicitud de retiro
                </button>
                <a
                    href="{{ url('/') }}"
                    class="block w-full rounded-lg border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Cancelar
                </a>
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

            const lat = parseFloat(latInput?.value) || defaultLat;
            const lng = parseFloat(lngInput?.value) || defaultLng;

            const map = L.map('map-solicitud-retiro').setView([lat, lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

            function setCoords(newLat, newLng, recenter = false) {
                const fixedLat = Number(newLat).toFixed(6);
                const fixedLng = Number(newLng).toFixed(6);
                latInput.value = fixedLat;
                lngInput.value = fixedLng;
                marker.setLatLng([fixedLat, fixedLng]);
                if (recenter) {
                    map.setView([fixedLat, fixedLng], map.getZoom());
                }
            }

            map.on('click', (e) => setCoords(e.latlng.lat, e.latlng.lng, false));
            marker.on('dragend', () => {
                const pos = marker.getLatLng();
                setCoords(pos.lat, pos.lng, false);
            });
            btnCentrar?.addEventListener('click', () => setCoords(defaultLat, defaultLng, true));

            setTimeout(() => map.invalidateSize(), 200);
        });
    </script>
@endpush