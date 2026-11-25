@extends('layouts.app')

@section('title', 'Mis retiros')

{{-- Vista funcional (T33). Alex le da el estilo final en T45. --}}
@section('content')
<div class="bg-white shadow-sm sm:rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Mis retiros</h1>
    <p class="text-gray-600 mb-4">Retiros que te asignó el administrador. Al terminar, cargá el peso real de cada material.</p>

    @if (session('status'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">{{ $errors->first() }}</div>
    @endif

    @forelse ($retiros as $retiro)
        <article class="border border-gray-200 rounded-lg p-4 mb-4">
            <div class="flex flex-wrap justify-between gap-2 mb-2">
                <h2 class="font-semibold text-gray-900">
                    #{{ $retiro->id }} · {{ $retiro->vecino->name }} {{ $retiro->vecino->apellido }}
                </h2>
                <span class="text-sm px-2 py-1 rounded-full bg-gray-100">{{ $retiro->estado->etiqueta() }}</span>
            </div>
            <p class="text-sm text-gray-700">
                {{ $retiro->direccion }}@if ($retiro->barrio), {{ $retiro->barrio->nombre }}@endif
                · {{ $retiro->fecha_preferida->format('d/m/Y') }} ({{ $retiro->franja_horaria }})
                · Tel. {{ $retiro->telefono_contacto }}
            </p>
            @if ($retiro->observaciones)
                <p class="text-sm text-gray-500 mt-1">{{ $retiro->observaciones }}</p>
            @endif

            @if ($retiro->estado === \App\Enums\EstadoSolicitud::EnProceso)
                @can('completar', $retiro)
                    <form method="POST" action="{{ route('recolector.retiros.completar', $retiro) }}" class="mt-3 space-y-2">
                        @csrf
                        @method('PATCH')
                        @foreach ($retiro->materiales as $material)
                            <label class="flex items-center gap-3 text-sm">
                                <span class="flex-1">{{ $material->categoria->nombre }}: {{ $material->descripcion }}</span>
                                <input type="number" name="pesos[{{ $material->id }}]" step="0.01" min="0" required
                                       value="{{ old('pesos.'.$material->id) }}"
                                       class="w-28 px-2 py-1 border border-gray-300 rounded-md" aria-label="Kg de {{ $material->descripcion }}">
                                <span>kg</span>
                            </label>
                        @endforeach
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Completar retiro</button>
                    </form>
                @endcan
            @else
                <ul class="mt-2 text-sm text-gray-600 list-disc pl-5">
                    @foreach ($retiro->materiales as $material)
                        <li>{{ $material->categoria->nombre }}: {{ $material->descripcion }}@if ($material->pivot->peso_real_kg !== null) — {{ $material->pivot->peso_real_kg }} kg @endif</li>
                    @endforeach
                </ul>
            @endif
        </article>
    @empty
        <p class="text-gray-600">No tenés retiros asignados por ahora.</p>
    @endforelse

    {{ $retiros->links() }}
</div>
@endsection
