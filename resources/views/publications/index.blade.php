@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Cabecera de la sección -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Mis Publicaciones</h1>
            <p class="text-gray-600 text-sm mt-1">Gestiona los materiales que has publicado para reciclar en BarrioVerde.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('publications.create') }}" 
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Publicación
            </a>
        </div>
    </div>

    <!-- Contenido: Lista de Publicaciones o Estado Vacío -->
    @if(count($publicaciones) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-200">
                @foreach($publicaciones as $pub)
                    <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between hover:bg-gray-50 transition">
                        <!-- Información Principal -->
                        <div class="space-y-1 mb-4 sm:mb-0">
                            <div class="flex items-center space-x-3">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pub['status_class'] }}">
                                    {{ $pub['status'] }}
                                </span>
                                <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">
                                    {{ $pub['category'] }}
                                </span>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ $pub['title'] }}
                            </h2>
                            <p class="text-sm text-gray-600 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $pub['address'] }} • <span class="font-medium text-gray-700 ml-1">Cant: {{ $pub['quantity'] }}</span>
                            </p>
                            <p class="text-xs text-gray-400">Publicado el {{ $pub['created_at'] }}</p>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex items-center space-x-3 self-end sm:self-center">
                            <button type="button" class="px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                                Editar
                            </button>
                            <button type="button" class="px-3 py-1.5 border border-red-200 rounded-lg text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 transition">
                                Eliminar
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Estado Vacío -->
        <div class="text-center bg-white rounded-xl border border-gray-200 p-12 shadow-sm">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No tienes publicaciones activas</h3>
            <p class="mt-1 text-sm text-gray-500">Comienza publicando materiales reciclables para ayudar a la comunidad.</p>
            <div class="mt-6">
                <a href="{{ route('publications.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700">
                    Crear publicación
                </a>
            </div>
        </div>
    @endif
</div>
@endsection