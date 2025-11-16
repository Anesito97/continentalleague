@php
    // MEJORA: Lógica de gradientes para el estado
    $statusGradient = $match->estado === 'finalizado' 
        ? 'bg-gradient-to-r from-blue-600 to-indigo-700' 
        : 'bg-gradient-to-r from-primary to-emerald-600';

    $score =
        $match->estado === 'finalizado'
            ? "{$match->goles_local} - {$match->goles_visitante}"
            : $match->fecha_hora->format('H:i');
@endphp

{{-- MEJORA: "Glassmorphism" aplicado a la tarjeta, con borde y hover sutil --}}
<div
    class="p-3 bg-card-bg/80 backdrop-blur-lg rounded-lg shadow-xl border border-white/10 transition-colors duration-200 hover:bg-card-bg/90">

    {{-- ENCABEZADO DE RESULTADO Y ESTADO --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-2">

        {{-- Equipos --}}
        {{-- MEJORA: Fuente más grande y en negrita para los equipos --}}
        <span class="font-bold text-lg text-white mb-2 sm:mb-0 w-full sm:w-1/2">
            <img src="{{ $match->localTeam->escudo_url ?? asset('images/placeholder.png') }}"
                class="w-6 h-6 inline-block rounded-full mr-2">
            {{ $match->localTeam->nombre ?? 'N/A' }} <span class="text-gray-400 font-normal">vs</span>
            <img src="{{ $match->visitorTeam->escudo_url ?? asset('images/placeholder.png') }}"
                class="w-6 h-6 inline-block rounded-full mr-2">
            {{ $match->visitorTeam->nombre ?? 'N/A' }}
        </span>

        {{-- Resultado / Hora / Estado --}}
        <div class="flex items-center space-x-3 text-right">
            {{-- MEJORA: Etiqueta con gradiente y sombra --}}
            <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $statusGradient }} text-white shadow-md">
                {{ ucfirst($match->estado) }}
            </span>
            {{-- MEJORA: Marcador/Hora más grande y con color condicional --}}
            <span
                class="text-2xl font-extrabold {{ $match->estado === 'finalizado' ? 'text-yellow-400' : 'text-primary' }}">
                {{ $score }}
            </span>

            {{-- Botón de Acción (Admin) --}}
            @if ($match->estado === 'pendiente' && session('is_admin'))
                {{-- MEJORA: Botón con estética "glassmorphism" --}}
                <a href="{{ route('admin.finalize-match', ['match_id' => $match->id]) }}"
                    class="bg-red-600/80 hover:bg-red-600 text-white px-2 py-1 rounded-md text-xs backdrop-blur-sm border border-red-500/50 transition-all">Finalizar</a>
            @endif
        </div>
    </div>

    {{-- CUERPO DE EVENTOS DETALLADOS (SOLO SI FINALIZADO) --}}
    @if ($match->estado === 'finalizado')
        {{-- MEJORA: Borde superior con el color del nuevo tema --}}
        <div class="pt-3 mt-3 border-t border-white/10">
            <h5 class="text-sm font-semibold text-gray-400 mb-2">Detalle de Eventos</h5>
            <div class="flex text-xs text-gray-400">
                {{-- Columna Local --}}
                {{-- MEJORA: Borde derecho con el color del nuevo tema --}}
                <div class="w-1/2 pr-2 space-y-1 border-r border-white/10">
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