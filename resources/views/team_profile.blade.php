@extends('index')

@section('content')
    <div class="max-w-7xl mx-auto py-8 w-full">

        {{-- CABECERA: LOGO, TÍTULO Y ESTADÍSTICAS PRINCIPALES --}}
        <div class="card p-6 shadow-2xl mb-8 border-b-4 border-secondary">
            <div class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-8">

                {{-- Logo Principal --}}
                <img src="{{ $equipo->escudo_url ?? asset('images/placeholder.png') }}" alt="Logo {{ $equipo->nombre }}"
                    class="w-32 h-32 rounded-full object-cover border-4 border-primary shadow-lg flex-shrink-0" />

                {{-- Información del Equipo --}}
                <div class="flex-grow text-center md:text-left">
                    <h1 class="text-5xl font-extrabold text-white mb-2">{{ $equipo->nombre }}</h1>
                    <h1 class="text-lg font-bold text-gray-400 mb-2">
                        Posición en la liga:
                        <span class="text-primary text-2xl ml-2">{{ $leaguePosition }}º</span>
                    </h1>

                    {{-- Bloque de Métricas Rápidas --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 text-center">
                        <div class="bg-gray-700/50 p-3 rounded-lg">
                            <span class="text-xl font-bold block">{{ $equipo->partidos_jugados }}</span>
                            <span class="text-xs text-gray-400">PJ</span>
                        </div>
                        <div class="bg-gray-700/50 p-3 rounded-lg">
                            <span class="text-xl font-bold block text-green-400">{{ $equipo->ganados }}</span>
                            <span class="text-xs text-gray-400">Ganados</span>
                        </div>
                        <div class="bg-gray-700/50 p-3 rounded-lg">
                            <span class="text-xl font-bold block text-red-400">{{ $equipo->perdidos }}</span>
                            <span class="text-xs text-gray-400">Perdidos</span>
                        </div>
                        <div class="bg-gray-700/50 p-3 rounded-lg">
                            <span class="text-xl font-bold block text-yellow-400">{{ $equipo->goles_a_favor }}</span>
                            <span class="text-xs text-gray-400">Goles (GF)</span>
                        </div>
                    </div>
                </div>

                {{-- Bloque de Jugadores Clave (Goleador y Asistente) --}}
                <div class="w-full md:w-1/3 grid grid-cols-2 gap-4">
                    {{-- ⬇️ Métrica Destacada (Top Goleador) - EXISTENTE ⬇️ --}}
                    @if ($topScorer)
                        <div class="bg-primary/20 p-4 rounded-lg text-center shadow-inner">
                            <span class="text-sm font-semibold text-primary block mb-1">GOLEADOR</span>
                            <img src="{{ $topScorer->foto_url ?? asset('images/placeholder_jug.png') }}"
                                class="w-16 h-16 rounded-full object-cover mx-auto mb-2 border-2 border-primary" />
                            <p class="text-lg font-extrabold leading-tight">{{ $topScorer->nombre }}</p>
                            <p class="text-sm text-red-400">{{ $topScorer->goles }} Goles</p>
                        </div>
                    @endif

                    {{-- ⬇️ NUEVA MÉTRICA: Top Asistente ⬇️ --}}
                    @if (isset($topAssist))
                        <div class="bg-yellow-800/20 p-4 rounded-lg text-center shadow-inner">
                            <span class="text-sm font-semibold text-yellow-400 block mb-1">ASISTENTE CLAVE</span>
                            <img src="{{ $topAssist->foto_url ?? asset('images/placeholder_jug.png') }}"
                                class="w-16 h-16 rounded-full object-cover mx-auto mb-2 border-2 border-yellow-400" />
                            <p class="text-lg font-extrabold leading-tight">{{ $topAssist->nombre }}</p>
                            <p class="text-sm text-yellow-400">{{ $topAssist->asistencias }} Asist.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div> {{-- Fin Cabecera --}}


        {{-- ---------------------------------------------------- --}}
        {{-- SECCIÓN ANALÍTICA Y RECURSOS --}}
        {{-- ---------------------------------------------------- --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Columna 1: LISTA DE JUGADORES (Tabla) --}}
            <div class="lg:col-span-2 card p-4 shadow-xl">
                <h3 class="text-2xl font-bold mb-4 text-primary">Plantilla y Estadísticas</h3>

                @php
                    // Contamos el total de jugadores
                    $totalPlayers = $equipo->jugadores->count();
                    $limit = 4; // Límite de filas a mostrar inicialmente
                @endphp

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 bg-gray-800">
                                <th class="py-3 px-2">#</th>
                                <th class="py-3 px-2">Jugador</th>
                                <th class="py-3 px-2 text-center">POS</th>
                                <th class="py-3 px-2 text-center">⚽</th>
                                <th class="py-3 px-2 text-center">👟</th>
                                <th class="py-3 px-2 text-center">🧤</th>
                                <th class="py-3 px-2 text-center">🟥</th>
                                <th class="py-3 px-2 text-center">A</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800 text-sm">
                            @forelse($equipo->jugadores as $player)
                                @php
                                    // ⬇️ Lógica para ocultar filas después del límite ⬇️
                                    $isHidden = $loop->iteration > $limit ? 'player-row hidden' : 'player-row';
                                @endphp

                                <tr class="hover:bg-gray-700 transition {{ $isHidden }}">
                                    <td class="py-3 px-2 font-bold text-gray-300">{{ $player->numero }}</td>
                                    <td class="py-3 px-2 flex items-center">
                                        <img src="{{ $player->foto_url ?? asset('images/placeholder_jug.png') }}"
                                            class="w-8 h-8 rounded-full object-cover mr-3" />
                                        <span class="font-medium text-white">{{ $player->nombre }}</span>
                                    </td>
                                    <td class="py-3 px-2 text-center text-gray-400">{{ ucfirst($player->posicion) }}</td>
                                    <td class="py-3 px-2 text-center text-red-400 font-bold">{{ $player->goles }}</td>
                                    <td class="py-3 px-2 text-center text-yellow-400 font-bold">{{ $player->asistencias }}
                                    </td>
                                    <td class="py-3 px-2 text-center">{{ $player->paradas }}</td>
                                    <td class="py-3 px-2 text-center text-red-500">{{ $player->rojas }}</td>
                                    <td class="py-3 px-2 text-center text-yellow-300">{{ $player->amarillas }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-4 text-center text-gray-500">No hay jugadores registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- BOTÓN DE TOGGLE (Solo si hay más jugadores que el límite) --}}
                @if ($totalPlayers > $limit)
                    <button id="toggle-players-btn" onclick="togglePlayerList()"
                        class="mt-4 w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 rounded-lg transition text-sm">
                        Ver Plantilla Completa ({{ $totalPlayers }} jugadores)
                    </button>
                @endif
            </div>

            {{-- Columna 2: Récords del Equipo e Historial Reciente --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Bloque de Rendimiento Avanzado (ACTUALIZADO) --}}
                <div class="card p-4 shadow-xl">
                    <h4 class="text-xl font-bold mb-3 text-primary">Rendimiento Avanzado (xPJ)</h4>
                    <div class="space-y-3">
                        <p class="text-gray-300 flex justify-between">Ratio Victoria: <span
                                class="font-bold text-green-400">{{ $winRatio }}%</span></p>
                        <p class="text-gray-300 flex justify-between">Eficiencia Ofensiva: <span
                                class="font-bold text-yellow-400">{{ $offensiveEfficiency }}%</span></p>
                        <p class="text-gray-300 flex justify-between">Goles Promedio/PJ: <span
                                class="font-bold text-yellow-400">{{ $gpj }}</span></p>
                        <p class="text-gray-300 flex justify-between">Goles Recibidos/PJ: <span
                                class="font-bold text-blue-400">{{ $gcpj }}</span></p>
                        <p class="text-gray-300 flex justify-between">Tarjetas Promedio/PJ: <span
                                class="font-bold text-red-400">{{ $cardsRatio }}</span></p>
                        <p class="text-gray-300 flex justify-between border-t border-gray-700 pt-3 mt-3">Diferencia Total:
                            <span
                                class="font-bold {{ $goalDifference >= 0 ? 'text-green-400' : 'text-red-400' }}">{{ $goalDifference > 0 ? '+' : '' }}{{ $goalDifference }}</span>
                        </p>
                    </div>
                </div>
                {{-- ⬇️ NUEVO BLOQUE: FORTALEZA LOCAL/VISITANTE ⬇️ --}}
                <div class="card p-4 shadow-xl">
                    <h4 class="text-xl font-bold mb-3 text-secondary">Análisis Local/Visitante</h4>
                    {{-- Nota: Aquí simularíamos los datos, se asume que los calculaste en el controlador --}}
                    <div class="space-y-3">
                        <p class="text-gray-300 flex justify-between font-semibold">Fortaleza en Casa: <span
                                class="text-sm text-green-400"> (Faltan Datos)</span></p>
                        <p class="text-gray-300 flex justify-between">PJ Local: <span
                                class="font-bold">{{ $equipo->localMatches->count() }}</span></p>
                        <p class="text-gray-300 flex justify-between">PJ Visitante: <span
                                class="font-bold">{{ $equipo->visitorMatches->count() }}</span></p>
                    </div>
                </div>

                {{-- ⬇️ NUEVO BLOQUE: DISCIPLINA Y RECURSOS --}}
                <div class="card p-4 shadow-xl">
                    <h4 class="text-xl font-bold mb-3 text-secondary">Análisis Profundo</h4>
                    <div class="space-y-3">
                        <p class="text-gray-300 flex justify-between">Goles de Delanteros: <span
                                class="font-bold text-red-400">{{ $golesDelanterosRatio }}%</span></p>
                        <p class="text-gray-300 flex justify-between">Goles de Medios/Defensas: <span
                                class="font-bold text-gray-300">{{ 100 - $golesDelanterosRatio }}%</span></p>
                        <p class="text-gray-300 flex justify-between border-t border-gray-700 pt-3 mt-3">Total Paradas:
                            <span class="font-bold text-blue-400">{{ $totalParadas }}</span>
                        </p>
                        <p class="text-gray-300 flex justify-between">Tarjetas (T): <span
                                class="font-bold text-red-500">{{ $totalCards }}</span></p>
                    </div>
                </div>

                {{-- Bloque de Disciplina y Jugadores --}}
                <div class="card p-4 shadow-xl">
                    <h4 class="text-xl font-bold mb-3 text-secondary">Disciplina y Recursos</h4>
                    <div class="space-y-3">
                        <p class="text-gray-300 flex justify-between">Total Jugadores: <span
                                class="font-bold text-gray-300">{{ $totalPlayers }}</span></p>
                        <p class="text-gray-300 flex justify-between">Total Asistencias: <span
                                class="font-bold text-yellow-400">{{ $totalAsistencias }}</span></p>
                        <p class="text-gray-300 flex justify-between">Total Tarjetas Amarillas: <span
                                class="font-bold text-yellow-300">{{ $totalYellows }}</span></p>
                        <p class="text-gray-300 flex justify-between">Total Tarjetas Rojas: <span
                                class="font-bold text-red-500">{{ $totalReds }}</span></p>
                    </div>
                </div>

                {{-- Historial Reciente y Racha --}}
                <div class="card p-4 shadow-xl">
                    <h4 class="text-xl font-bold mb-3 text-secondary">Racha y Fair Play</h4>

                    {{-- Top 3 Fair Play --}}
                    <h5 class="text-sm font-semibold text-gray-400 mb-2">Jugadores Más Limpios (Top 3)</h5>
                    <ul class="space-y-1">
                        @forelse($topCleanPlayers as $player)
                            <li class="text-gray-300 flex justify-between text-sm">
                                <span>{{ $player->nombre }}</span>
                                <span class="font-bold text-green-400">{{ $player->discipline_score }} Pts</span>
                            </li>
                        @empty
                            <p class="text-sm text-gray-500">No hay datos de disciplina.</p>
                        @endforelse
                    </ul>

                    {{-- Guía de Forma/Racha --}}
                    <div class="flex items-center space-x-2 mt-4 pt-4 border-t border-gray-700">
                        <span class="text-sm font-semibold text-gray-400">Última Racha:</span>
                        <div class="flex space-x-1">
                            @foreach (str_split($streak) as $result)
                                @php
                                    $resultClass =
                                        $result === 'G'
                                            ? 'bg-green-600'
                                            : ($result === 'E'
                                                ? 'bg-yellow-500'
                                                : ($result === 'P'
                                                    ? 'bg-red-600'
                                                    : 'bg-gray-500'));
                                @endphp
                                <span
                                    class="w-6 h-6 flex items-center justify-center rounded-sm text-xs font-bold text-white {{ $resultClass }}">{{ $result }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Lista de Partidos (Se mantiene) --}}
                    <h5 class="text-sm font-semibold text-gray-400 mt-4 mb-2 border-t border-gray-700 pt-3">Historial
                        Reciente</h5>
                    <ul class="space-y-2 text-gray-300">
                        @forelse($recentHistory as $match)
                            @php
                                $isLocal = $match->equipo_local_id === $equipo->id;
                                $result =
                                    $match->goles_local === $match->goles_visitante
                                        ? 'E'
                                        : (($isLocal && $match->goles_local > $match->goles_visitante) ||
                                        (!$isLocal && $match->goles_visitante > $match->goles_local)
                                            ? 'G'
                                            : 'P');
                                $resultClass =
                                    $result === 'G'
                                        ? 'bg-green-600'
                                        : ($result === 'E'
                                            ? 'bg-yellow-500'
                                            : 'bg-red-600');
                                $opponent = $isLocal ? $match->visitorTeam->nombre : $match->localTeam->nombre;
                                $score = "{$match->goles_local} - {$match->goles_visitante}";
                            @endphp
                            <li class="flex justify-between items-center bg-gray-800 p-2 rounded-md">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-bold {{ $resultClass }}">{{ $result }}</span>
                                <span class="text-sm">vs {{ $opponent }}</span>
                                <span class="font-bold text-sm">{{ $score }}</span>
                            </li>
                        @empty
                            <p class="text-sm text-gray-500">No hay historial de partidos finalizados.</p>
                        @endforelse
                    </ul>
                </div>

            </div>
        </div>
    </div>
    <script>
        function togglePlayerList() {
            const rows = document.querySelectorAll('.player-row');
            const button = document.getElementById('toggle-players-btn');
            let isExpanded = button.dataset.expanded === 'true';

            rows.forEach(row => {
                // Mostrar u ocultar las filas que tienen la clase 'hidden'
                if (row.classList.contains('hidden')) {
                    row.classList.remove('hidden');
                }
            });

            if (isExpanded) {
                // Si estaba expandido, ahora ocultamos (solo filas > 4)
                rows.forEach((row, index) => {
                    if (index >=
                        {{ $limit }}) { // Oculta filas de la 5ta en adelante (índice 4 en adelante)
                        row.classList.add('hidden');
                    }
                });
                button.textContent = 'Ver Plantilla Completa ({{ $totalPlayers }} jugadores)';
                button.dataset.expanded = 'false';
            } else {
                // Si estaba colapsado, ahora mostramos todo
                button.textContent = 'Ocultar Plantilla';
                button.dataset.expanded = 'true';
            }
        }
    </script>
@endsection
