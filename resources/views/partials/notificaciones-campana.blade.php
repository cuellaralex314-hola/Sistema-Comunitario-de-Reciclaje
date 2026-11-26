@php
    $notificacionesLista = $notificaciones ?? collect([
        (object) [
            'id' => 1,
            'titulo' => 'Recolector asignado a SOL-0101',
            'detalle' => 'Carlos Gómez fue asignado para el turno de 13:00 a 17:00.',
            'hace' => 'Hace 10 min',
            'leida' => false,
            'url' => route('home'),
        ],
        (object) [
            'id' => 2,
            'titulo' => 'Nueva solicitud pendiente (SOL-0104)',
            'detalle' => 'Barrio San Martín · Plástico, Papel/cartón y Vidrio.',
            'hace' => 'Hace 35 min',
            'leida' => false,
            'url' => route('admin.panel'),
        ],
        (object) [
            'id' => 3,
            'titulo' => 'Retiro completado (SOL-0096)',
            'detalle' => 'Se finalizó la recolección en Rivadavia 620.',
            'hace' => 'Ayer',
            'leida' => true,
            'url' => route('recolector.retiros.index'),
        ],
    ]);

    $noLeidasCount = $notificacionesLista->where('leida', false)->count();
@endphp

<div class="relative" id="campana-notificaciones-wrapper">
    <button
        type="button"
        id="btn-campana-notificaciones"
        aria-expanded="false"
        aria-controls="dropdown-notificaciones"
        class="relative inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition hover:border-emerald-300 hover:bg-emerald-50/40 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
        title="Notificaciones"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>

        @if ($noLeidasCount > 0)
            <span
                id="badge-no-leidas"
                class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-emerald-600 px-1 text-[11px] font-bold text-white shadow-sm"
            >
                {{ $noLeidasCount }}
            </span>
        @endif
    </button>

    <div
        id="dropdown-notificaciones"
        class="fixed inset-x-4 top-16 z-50 hidden rounded-xl border border-gray-200 bg-white shadow-lg sm:absolute sm:inset-x-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-[min(22rem,calc(100vw-2rem))]"
    >
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-semibold text-gray-800">Notificaciones</h2>
                @if ($noLeidasCount > 0)
                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                        {{ $noLeidasCount }} nuevas
                    </span>
                @endif
            </div>

            <span class="text-xs font-medium text-gray-400">
                Actualizadas
            </span>
        </div>

        <div class="max-h-80 divide-y divide-gray-100 overflow-y-auto">
            @forelse ($notificacionesLista as $notif)
                <a
                    href="{{ $notif->url ?? '#' }}"
                    class="block px-4 py-3 transition hover:bg-gray-50 {{ empty($notif->leida) ? 'bg-emerald-50/30' : '' }}"
                >
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-xs font-semibold text-gray-800">
                            @if (empty($notif->leida))
                                <span class="mr-1.5 inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                            @endif
                            {{ $notif->titulo }}
                        </p>
                        <span class="shrink-0 text-[11px] text-gray-400">{{ $notif->hace }}</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ $notif->detalle }}</p>
                </a>
            @empty
                <div class="px-4 py-8 text-center text-xs text-gray-500">
                    No tenés notificaciones por el momento.
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const wrapper = document.getElementById('campana-notificaciones-wrapper');
        const btn = document.getElementById('btn-campana-notificaciones');
        const dropdown = document.getElementById('dropdown-notificaciones');

        if (!wrapper || !btn || !dropdown) return;

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden', !isHidden);
            btn.setAttribute('aria-expanded', String(isHidden));
        });

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                dropdown.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    });
</script>