@extends('layouts.app')

@section('title', 'Guía de Categorías de Reciclaje')

@section('content')
<div class="space-y-8">
    <div class="text-center max-w-2xl mx-auto space-y-4">
        <h1 class="text-3xl font-bold text-gray-900">Guía de Categorías y Residuos</h1>
        <p class="text-gray-600">Consulta los materiales aceptados, sus condiciones de entrega y ubica cómo clasificarlos.</p>
        
        <form method="GET" action="{{ route('categories.index') }}" class="flex gap-2 max-w-lg mx-auto pt-2">
            <input type="text" name="search" placeholder="Buscar material (ej. botellas, cartón, pilas)..." 
                   value="{{ request('search') }}"
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-2 rounded-lg transition">
                Buscar
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-3">
            <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center font-bold text-xl">
                ♻️
            </div>
            <h3 class="text-xl font-semibold text-gray-800">Plásticos (PET / PEAD)</h3>
            <p class="text-sm text-gray-600">Botellas de agua, gaseosas, envases de detergente y shampoo.</p>
            <div class="bg-yellow-50 p-3 rounded-md text-xs text-yellow-800">
                <strong>Condición:</strong> Limpios, secos y aplastados.
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-3">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold text-xl">
                📦
            </div>
            <h3 class="text-xl font-semibold text-gray-800">Papel y Cartón</h3>
            <p class="text-sm text-gray-600">Cajas, diarios, revistas, hojas de oficio y carpetas de cartón.</p>
            <div class="bg-blue-50 p-3 rounded-md text-xs text-blue-800">
                <strong>Condición:</strong> Sin restos de comida ni mojados.
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-3">
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center font-bold text-xl">
                🍾
            </div>
            <h3 class="text-xl font-semibold text-gray-800">Vidrio</h3>
            <p class="text-sm text-gray-600">Botellas de vidrio, frascos de mermelada y conservas.</p>
            <div class="bg-green-50 p-3 rounded-md text-xs text-green-800">
                <strong>Condición:</strong> Enjuagados y sin tapas metálicas.
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-3">
            <div class="w-12 h-12 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center font-bold text-xl">
                🥫
            </div>
            <h3 class="text-xl font-semibold text-gray-800">Metales</h3>
            <p class="text-sm text-gray-600">Latas de aluminio (gaseosa/cerveza) y latas de conserva.</p>
            <div class="bg-gray-50 p-3 rounded-md text-xs text-gray-800">
                <strong>Condición:</strong> Enjuagadas y compactadas.
            </div>
        </div>

    </div>
</div>
@endsection