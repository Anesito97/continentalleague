@php
    $statusColor = $match->estado === 'finalizado' ? 'bg-blue-600' : 'bg-primary';
    $score =
        $match->estado === 'finalizado'
            ? "{$match->goles_local} - {$match->goles_visitante}"
            : $match->fecha_hora->format('H:i');
@endphp

<div class="p-3 bg-gray-800 rounded-lg shadow-md">

    {{-- ENCABEZADO DE RESULTADO Y ESTADO --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-2">

        {{-- Equipos --}}
        <span class="font-medium text-white mb-2 sm:mb-0 w-full sm:w-1/2">
            <img src="{{ $match->localTeam->escudo_url ?? asset('images/placeholder.png') }}"
                class="w-6 h-6 inline-block rounded-full mr-2">
            {{ $match->localTeam->nombre ?? 'N/A' }} <span class="text-gray-400 font-normal">vs</span>
            <img src="{{ $match->visitorTeam->escudo_url ?? asset('images/placeholder.png') }}"
                class="w-6 h-6 inline-block rounded-full mr-2">
            {{ $match->visitorTeam->nombre ?? 'N/A' }}
        </span>

        {{-- Resultado / Hora / Estado --}}
        <div class="flex items-center space-x-3 text-right">
            <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $statusColor }} text-white">
                {{ ucfirst($match->estado) }}
            </span>
            <span class="text-lg font-bold text-yellow-400">
                {{ $score }}
            </span>

            {{-- Botón de Acción (Admin) --}}
            @if ($match->estado === 'pendiente' && session('is_admin'))
                <a href="{{ route('admin.finalize-match', ['match_id' => $match->id]) }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-md text-xs">Finalizar</a>
            @endif
        </div>
    </div>

    {{-- CUERPO DE EVENTOS DETALLADOS (SOLO SI FINALIZADO) --}}
    @if ($match->estado === 'finalizado')
        <div class="pt-3 mt-3 border-t border-gray-700">
            <h5 class="text-sm font-semibold text-gray-400 mb-2">Detalle de Eventos</h5>
            <div class="flex text-xs text-gray-400">
                {{-- Columna Local --}}
                <div class="w-1/2 pr-2 space-y-1 border-r border-gray-600">
                    @php
                        // Un evento es del equipo LOCAL si:
                        // 1. Lo hizo un jugador local Y NO es un gol en contra.
                        // 2. Lo hizo un jugador visitante Y SÍ es un gol en contra.
                        $localEvents = $match->eventos
                            ->filter(function ($event) use ($match) {
                                $isOwnGoal = strtolower($event->goal_type ?? '') === 'en contra';
                                return ($event->equipo_id == $match->equipo_local_id && !$isOwnGoal) ||
                                    ($event->equipo_id == $match->equipo_visitante_id && $isOwnGoal);
                            })
                            ->sortBy('minuto');
                    @endphp

                    @forelse($localEvents as $event)
                        @include('partials.event_item', ['event' => $event, 'align' => 'right'])
                    @empty
                        <div class="text-gray-500 text-right pr-2">Sin eventos</div>
                    @endforelse
                </div>

                {{-- Columna Visitante --}}
                <div class="w-1/2 pl-2 space-y-1">
                    @php
                        // Un evento es del equipo VISITANTE si:
                        // 1. Lo hizo un jugador visitante Y NO es un gol en contra.
                        // 2. Lo hizo un jugador local Y SÍ es un gol en contra.
                        $visitorEvents = $match->eventos
                            ->filter(function ($event) use ($match) {
                                $isOwnGoal = strtolower($event->goal_type ?? '') === 'en contra';
                                return ($event->equipo_id == $match->equipo_visitante_id && !$isOwnGoal) ||
                                    ($event->equipo_id == $match->equipo_local_id && $isOwnGoal);
                            })
                            ->sortBy('minuto');
                    @endphp

                    @forelse($visitorEvents as $event)
                        @include('partials.event_item', ['event' => $event, 'align' => 'left'])
                    @empty
                        <div class="text-gray-500 text-left pl-2">Sin eventos</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
