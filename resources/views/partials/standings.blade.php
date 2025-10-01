<h2 class="text-4xl font-bold text-white mb-6 border-b border-green-700 pb-2">Tabla de Posiciones</h2>


{{-- ---------------------------------------------------- --}}
{{-- 2. TABLA DE CLASIFICACIÓN --}}
{{-- ---------------------------------------------------- --}}
<div class="card p-6">
    <h3 class="text-2xl font-bold mb-4 text-white">Clasificación de Liga</h3>
    <p class="text-xs text-gray-500 mb-3 sm:hidden">Desliza horizontalmente la tabla para ver todos los datos (GF, GC).
    </p>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 bg-gray-800">
                    <th class="py-3 px-2">#</th>
                    <th class="py-3 px-2">Equipo</th>
                    <th class="py-3 px-2 text-center">Ptos</th>
                    <th class="py-3 px-2 text-center">PJ</th>
                    <th class="py-3 px-2 text-center">G</th>
                    <th class="py-3 px-2 text-center hidden sm:table-cell">E</th>
                    <th class="py-3 px-2 text-center hidden sm:table-cell">P</th>
                    <th class="py-3 px-2 text-center">GF</th>
                    <th class="py-3 px-2 text-center">GC</th>
                    <th class="py-3 px-2 text-center">Dif</th>
                </tr>
            </thead>
            <tbody id="standings-body" class="divide-y divide-gray-800 text-sm">
                @forelse($teams as $index => $team)
                    @php
                        $positionClass = '';
                        if ($index === 0) {
                            $positionClass = 'bg-yellow-900/30 text-yellow-400 border-l-4 border-yellow-400';
                        } elseif ($index < 4) {
                            $positionClass = 'bg-green-900/30 text-green-400 border-l-4 border-green-400';
                        }

                        $goalDiff = $team->goles_a_favor - $team->goles_en_contra;
                    @endphp

                    <tr class="hover:bg-gray-700 transition {{ $positionClass }}">
                        <td class="py-3 px-2 font-bold">{{ $index + 1 }}</td>
                        <td class="py-3 px-2 flex items-center">
                            <img src="{{ $team->escudo_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=LOGO' }}"
                                onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=LOGO'"
                                class="w-8 h-8 rounded-full object-cover mr-3" />
                            <span class="font-medium text-white">{{ $team->nombre }}</span>
                        </td>
                        <td
                            class="py-3 px-2 text-center font-bold {{ $index < 4 ? 'text-yellow-300' : 'text-green-400' }}">
                            {{ $team->puntos }}</td>
                        <td class="py-3 px-2 text-center">{{ $team->partidos_jugados }}</td>
                        <td class="py-3 px-2 text-center">{{ $team->ganados }}</td>
                        <td class="py-3 px-2 text-center hidden sm:table-cell">{{ $team->empatados }}</td>
                        <td class="py-3 px-2 text-center hidden sm:table-cell">{{ $team->perdidos }}</td>
                        <td class="py-3 px-2 text-center">{{ $team->goles_a_favor }}</td>
                        <td class="py-3 px-2 text-center">{{ $team->goles_en_contra }}</td>
                        <td
                            class="py-3 px-2 text-center font-bold {{ $goalDiff > 0 ? 'text-green-500' : ($goalDiff < 0 ? 'text-red-500' : 'text-gray-400') }}">
                            {{ $goalDiff > 0 ? '+' : '' }}{{ $goalDiff }}
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-gray-700 transition">
                        <td colspan="10" class="py-4 text-center text-gray-500">Aún no hay equipos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ---------------------------------------------------- --}}
{{-- 1. PANEL DE PARTIDOS (Próximos y Recientes) --}}
{{-- ---------------------------------------------------- --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

    {{-- PRÓXIMOS PARTIDOS (PENDIENTES) --}}
    <div class="card p-4">
        {{-- ⬇️ CORRECCIÓN: Título con botón "Ver todos" ⬇️ --}}
        <h3 class="flex justify-between items-center text-xl font-semibold mb-3 text-green-400">
            <span>Próximos Partidos</span>
            <button
                class="bg-gray-600 hover:bg-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold transition">
                Ver todos
            </button>
        </h3>
        <div class="space-y-3">
            @forelse($recentMatches->where('estado', 'pendiente')->take(5) as $match)
                @php
                    $dateTime = \Carbon\Carbon::parse($match->fecha_hora);
                    $isLive = $dateTime->isPast() && $dateTime->diffInHours(now()) < 2; // Simulación: Si empezó hace < 2h
                @endphp
                <div
                    class="p-3 border-l-4 rounded-md flex justify-between items-center text-sm 
                            {{ $isLive ? 'border-red-500 bg-gray-600' : 'border-green-500 bg-gray-700' }}">

                    <div class="flex flex-col">
                        <span class="font-bold text-white">
                            {{ $match->localTeam->nombre }} <span class="font-normal text-gray-400">vs</span>
                            {{ $match->visitorTeam->nombre }}
                        </span>
                        @if ($isLive)
                            <span class="text-xs text-red-400 font-bold">¡EN CURSO!</span>
                        @endif
                    </div>

                    <span class="text-xs text-gray-400 text-right">
                        {{ $dateTime->locale('es')->isoFormat('ddd, D MMM') }}<br>
                        <span class="font-semibold">{{ $dateTime->format('H:i') }}</span>
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400">No hay partidos pendientes.</p>
            @endforelse
        </div>
    </div>

    {{-- ÚLTIMOS PARTIDOS (FINALIZADOS) --}}
    <div class="card p-4">
        {{-- ⬇️ CORRECCIÓN: Título con botón "Ver todos" ⬇️ --}}
        <h3 class="flex justify-between items-center text-xl font-semibold mb-3 text-blue-400">
            <span>Últimos Resultados</span>
            <button
                class="bg-gray-600 hover:bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-semibold transition">
                Ver todos
            </button>
        </h3>
        <div class="space-y-4">
            @forelse($recentMatches->where('estado', 'finalizado')->take(5) as $match)
                <div class="p-3 border-l-4 border-blue-500 bg-gray-700 rounded-md text-sm">

                    {{-- ⬇️ CORRECCIÓN: Usamos un contenedor Flexbox para el encabezado ⬇️ --}}
                    <div class="flex justify-between items-center mb-2">

                        {{-- 1. Nombre Equipo Local --}}
                        <span class="font-bold text-white text-left overflow-hidden whitespace-nowrap pr-1">
                            {{ $match->localTeam->nombre }}
                        </span>

                        {{-- 2. Resultado Central (Fijado al Centro) --}}
                        <span
                            class="text-base font-bold flex-shrink-0 mx-2 
                                     {{ $match->goles_local === $match->goles_visitante ? 'text-yellow-400' : 'text-blue-400' }}">
                            {{ $match->goles_local }} - {{ $match->goles_visitante }}
                        </span>

                        {{-- 3. Nombre Equipo Visitante --}}
                        <span class="font-bold text-white text-right overflow-hidden whitespace-nowrap pl-1">
                            {{ $match->visitorTeam->nombre }}
                        </span>
                    </div>

                    {{-- CUERPO: DETALLE DE EVENTOS (Side-by-Side) --}}
                    <div class="flex text-xs text-gray-400">

                        {{-- Columna Local (Prácticamente sin cambios) --}}
                        <div class="w-1/2 pr-2 space-y-1 border-r border-gray-600">
                            @php
                                $localEvents = $match->eventos
                                    ->where('equipo_id', $match->equipo_local_id)
                                    ->sortBy('minuto');
                            @endphp

                            @forelse($localEvents as $event)
                                @include('partials.event_item', ['event' => $event, 'align' => 'right'])
                            @empty
                                <div class="text-gray-500 text-right pr-2">Sin eventos</div>
                            @endforelse
                        </div>

                        {{-- Columna Visitante (Prácticamente sin cambios) --}}
                        <div class="w-1/2 pl-2 space-y-1">
                            @php
                                $visitorEvents = $match->eventos
                                    ->where('equipo_id', $match->equipo_visitante_id)
                                    ->sortBy('minuto');
                            @endphp

                            @forelse($visitorEvents as $event)
                                @include('partials.event_item', ['event' => $event, 'align' => 'left'])
                            @empty
                                <div class="text-gray-500 text-left pl-2">Sin eventos</div>
                            @endforelse
                        </div>
                    </div>

                    @if ($match->eventos->isEmpty())
                        <div class="pt-2 text-center text-gray-500">No se registraron eventos detallados.</div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-400">No hay resultados recientes.</p>
            @endforelse
        </div>
    </div>
</div>
