@extends('layouts.app')

@section('content')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<!-- Cambiamos max-w-6xl por max-w-7xl para aprovechar mejor el ancho de la pantalla -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Cabecera -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Puntos Verdes de Reciclaje</h1>
            <p class="text-gray-600 text-sm mt-1">Encuentra los centros de recepción, campanas y horarios habilitados para reciclar en la ciudad.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                🟢 Mapa interactivo activo
            </span>
        </div>
    </div>

    <!-- Contenedor Principal (Grid: Mapa más grande en col-span-7 y Panel en col-span-5) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Mapa (Ocupa 7 columnas en escritorio y es más alto) -->
        <div class="lg:col-span-7 bg-white rounded-xl shadow-sm border border-gray-200 p-4 sticky top-6">
            <div id="map" style="height: 620px;" class="w-full rounded-lg z-10"></div>
        </div>

        <!-- Panel lateral con Puntos de Recepción (Ocupa 5 columnas en escritorio) -->
        <div class="lg:col-span-5 bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Puntos de Recepción ({{ count($puntos ?? []) }})</h2>
                
                <div class="space-y-4 max-h-[540px] overflow-y-auto pr-1">
                    <!-- Punto 1 -->
                    <div data-map-point data-lat="-26.1852348" data-lng="-58.1743959" data-zoom="15" class="p-3.5 rounded-lg border border-gray-100 bg-gray-50 hover:bg-green-50 transition cursor-pointer shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold px-2 py-0.5 bg-green-100 text-green-800 rounded">Plásticos y Papel</span>
                            <span class="text-xs font-medium text-green-600">Abierto</span>
                        </div>
                        <h3 class="font-medium text-gray-900 mt-1">Punto Verde Central (Plaza San Martín)</h3>
                        <p data-point-address class="text-xs text-gray-600 mt-0.5">Av. 25 de Mayo y Mitre</p>
                        <p data-point-hours class="text-xs text-gray-500 mt-1">🕒 Lunes a Sábados: 08:00 - 18:00 hs</p>
                    </div>

                    <!-- Punto 2: Instituto Pedagógico Provincial -->
                    <div data-map-point data-lat="-26.1444330" data-lng="-58.1551832" data-zoom="16" class="p-3.5 rounded-lg border border-gray-100 bg-gray-50 hover:bg-green-50 transition cursor-pointer shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold px-2 py-0.5 bg-blue-100 text-blue-800 rounded">Electrónicos y Pilas</span>
                            <span class="text-xs font-medium text-green-600">Abierto</span>
                        </div>
                        <h3 class="font-medium text-gray-900 mt-1">Instituto Pedagógico Provincial (IPP)</h3>
                        <p data-point-address class="text-xs text-gray-600 mt-0.5">Av. Vilagi</p>
                        <p data-point-hours class="text-xs text-gray-500 mt-1">🕒 Lunes a Viernes: 08:00 - 19:00 hs</p>
                    </div>

                    <!-- Punto 3 -->
                    <div data-map-point data-lat="-26.1833705" data-lng="-58.1686474" data-zoom="15" class="p-3.5 rounded-lg border border-gray-100 bg-gray-50 hover:bg-green-50 transition cursor-pointer shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded">Multimaterial</span>
                            <span class="text-xs font-medium text-red-500">Cerrado</span>
                        </div>
                        <h3 class="font-medium text-gray-900 mt-1">Punto Verde Barrio San Martín</h3>
                        <p data-point-address class="text-xs text-gray-600 mt-0.5">Barrio San Martín, Formosa</p>
                        <p data-point-hours class="text-xs text-gray-500 mt-1">🕒 Martes a Domingos: 10:00 - 19:00 hs</p>
                    </div>

                    <!-- Punto 4 -->
                    <div data-map-point data-lat="-26.1808177" data-lng="-58.1636922" data-zoom="15" class="p-3.5 rounded-lg border border-gray-100 bg-gray-50 hover:bg-green-50 transition cursor-pointer shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold px-2 py-0.5 bg-purple-100 text-purple-800 rounded">Vidrio y Aluminio</span>
                            <span class="text-xs font-medium text-green-600">Abierto</span>
                        </div>
                        <h3 class="font-medium text-gray-900 mt-1">Punto Verde Paseo Costanera</h3>
                        <p data-point-address class="text-xs text-gray-600 mt-0.5">Plaza de la Costanera, Formosa</p>
                        <p data-point-hours class="text-xs text-gray-500 mt-1">🕒 Lunes a Domingos: 09:00 - 21:00 hs</p>
                    </div>

                    <!-- Punto 5 -->
                    <div data-map-point data-lat="-26.1438314" data-lng="-58.1522081" data-zoom="15" class="p-3.5 rounded-lg border border-gray-100 bg-gray-50 hover:bg-green-50 transition cursor-pointer shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold px-2 py-0.5 bg-green-100 text-green-800 rounded">Orgánicos y Compost</span>
                            <span class="text-xs font-medium text-green-600">Abierto</span>
                        </div>
                        <h3 class="font-medium text-gray-900 mt-1">Punto Verde Barrio Eva Perón</h3>
                        <p data-point-address class="text-xs text-gray-600 mt-0.5">Barrio Eva Perón, Formosa</p>
                        <p data-point-hours class="text-xs text-gray-500 mt-1">🕒 Miércoles a Domingos: 08:00 - 16:00 hs</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-500">¿Conoces un nuevo punto de acopio en la ciudad?</p>
                <a href="#" class="text-xs font-medium text-green-600 hover:text-green-700 underline mt-1 inline-block">Sugerir un nuevo punto verde</a>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('map');

        // Capa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const pointCards = document.querySelectorAll('[data-map-point]');
        const markers = [];

        pointCards.forEach(card => {
            const lat = Number(card.dataset.lat);
            const lng = Number(card.dataset.lng);
            const title = card.querySelector('h3').textContent.trim();
            const address = card.querySelector('[data-point-address]').textContent.trim();
            const hours = card.querySelector('[data-point-hours]').textContent.trim();
            const marker = L.marker([lat, lng]).addTo(map);

            marker.bindPopup(`<b>${title}</b><br>${address}<br><small>${hours}</small>`);
            markers.push(marker);
            card.addEventListener('click', () => {
                map.setView([lat, lng], Number(card.dataset.zoom) || 15);
                marker.openPopup();
            });
        });

        if (markers.length > 0) {
            map.fitBounds(L.featureGroup(markers).getBounds().pad(0.1), { maxZoom: 14 });
        }

        // Forzar actualización del tamaño del mapa
        setTimeout(function() {
            map.invalidateSize();
        }, 200);
    });
</script>
@endsection