{{-- Este contenido se renderiza cuando $activeView es 'home' --}}

@php
    $totalMatches = $teams->sum('partidos_jugados') / 2;
    $totalGoals = $teams->sum('goles_a_favor');
    $totalTeams = $teams->count();
    $nextMatch = $pendingMatches->first();
    // Obtener los primeros 3 equipos para el detalle
    $topTeams = $teams->take(10);
    $avgGoals = $totalMatches > 0 ? number_format($totalGoals / $totalMatches, 2) : 0;
    $topImpactPlayer = $players->sortByDesc(fn($p) => $p->goles + $p->asistencias)->first();
    $injuredPlayers = $players->where('esta_lesionado', true);
@endphp

@include('partials.news_slider', ['newsItems' => $newsItems])

<h2 class="text-3xl font-bold mb-6">Resumen de la Liga</h2>

@if ($nextMatch)
    @php
        // Usamos el historial H2H para la probabilidad simple
        $localTeamId = $nextMatch->localTeam->id;
        $h2hLocalWins = $h2hRecord['G'] ?? 0;
        $h2hVisitorWins = $h2hRecord['P'] ?? 0;
        $h2hTotal = $h2hRecord['total'] ?? 0;

        // Probabilidad de Victoria Local (Basado en el historial H2H)
        $localProb = $prediction['localProb'];
        $visitorProb = $prediction['visitorProb'];
        $drawProb = $prediction['drawProb'];

        // Si no hay historial, usamos el 50/50 y lo etiquetamos como "Basado en Racha"
        $probTitle = $prediction['title'];

        $votedCookieName = 'voted_' . $nextMatch->id;
        $hasVoted = request()->cookie($votedCookieName);
        $votingStartTime = \Carbon\Carbon::parse($nextMatch->fecha_hora)->subHour();
        $isVotingActive = Carbon\Carbon::now()->lt($votingStartTime);
    @endphp

    {{-- ---------------------------------------------------- --}}
    {{-- 1. TARJETA DE DUELO DESTACADA (ANALÍTICA Y COMPACTA) --}}
    {{-- ---------------------------------------------------- --}}

    <h3 class="text-2xl font-bold mb-4 mt-8">Próximo Gran Duelo | Jornada {{ $nextMatch->jornada }}</h3>

    <div class="flex justify-center">
        <div class="card max-w-4xl w-full p-4 sm:p-6 shadow-2xl transition duration-500 hover:translate-y-0">

            {{-- ⬇️ 1. CONTENEDOR PRINCIPAL DEL DUELO (FLEX-ROW) ⬇️ --}}
            <div class="flex flex-row items-start justify-between space-x-2">

                {{-- 1. EQUIPO LOCAL --}}
                <div class="flex flex-col items-center w-5/12 text-center flex-shrink-0">
                    <a href="{{ route('team.profile', $nextMatch->localTeam->id) }}"
                        class="flex flex-col items-center hover:opacity-90 transition">
                        <img src="{{ $nextMatch->localTeam->escudo_url ?? 'https://placehold.co/100x100/1f2937/FFFFFF?text=LOCAL' }}"
                            alt="Logo Local"
                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-4 border-primary/50 mb-1" />
                        <span
                            class="text-sm sm:text-xl font-extrabold text-white overflow-hidden whitespace-nowrap max-w-full">{{ $nextMatch->localTeam->nombre }}</span>
                        <span class="text-[10px] text-gray-400">Local</span>
                    </a>
                </div>

                {{-- ⬇️ 2. CENTRO: VS Y JORNADA (Limpiado y Centrado) ⬇️ --}}
                <div class="w-2/12 flex flex-col items-center justify-start flex-shrink-0 pt-4 sm:pt-6">
                    <div class="py-1 mx-auto max-w-full">
                        <span class="text-5xl font-black text-red-500 block leading-none">VS</span>
                    </div>
                </div>

                {{-- 3. EQUIPO VISITANTE --}}
                <div class="flex flex-col items-center w-5/12 text-center flex-shrink-0">
                    <a href="{{ route('team.profile', $nextMatch->visitorTeam->id) }}"
                        class="flex flex-col items-center hover:opacity-90 transition">
                        <img src="{{ $nextMatch->visitorTeam->escudo_url ?? 'https://placehold.co/100x100/1f2937/FFFFFF?text=VISIT' }}"
                            alt="Logo Visitante"
                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-4 border-secondary/50 mb-1" />
                        <span
                            class="text-sm sm:text-xl font-extrabold text-white overflow-hidden whitespace-nowrap max-w-full">{{ $nextMatch->visitorTeam->nombre }}</span>
                        <span class="text-[10px] text-gray-400">Visitante</span>
                    </a>
                </div>
            </div>

            {{-- ⬇️ 2. SECCIÓN DE DATOS DE TIEMPO Y PROBABILIDAD (FILA COMPLETA) ⬇️ --}}
            <div class="w-full text-center mt-4 pt-4 border-t border-gray-700">

                {{-- Fecha y Hora (Jerarquía) --}}
                <div class="mb-4">
                    <span class="text-lg font-semibold text-white block mb-1">
                        {{ \Carbon\Carbon::parse($nextMatch->fecha_hora)->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                    </span>
                    <span class="text-sm text-gray-400 font-normal">
                        A las <span
                            class="font-bold text-white">{{ \Carbon\Carbon::parse($nextMatch->fecha_hora)->format('h:i A') }}</span>
                    </span>
                </div>

                {{-- ⬇️ CONTENEDOR DE ANÁLISIS Y ACCIÓN (GRID CONSOLIDADO) ⬇️ --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full mx-auto">

                    {{-- COLUMNA 1: PROBABILIDAD H2H (Análisis Estático) --}}
                    <div class="flex flex-col items-center p-3 border border-gray-700 rounded-lg">
                        <span class="text-sm font-semibold text-gray-400 block mb-2">{{ $probTitle }}</span>

                        {{-- Barra de Probabilidad H2H --}}
                        <div
                            class="flex w-full max-w-xs h-3 rounded-full overflow-hidden text-xs font-bold shadow-inner">
                            <div class="bg-green-500 flex items-center justify-center text-[10px]"
                                style="width: {{ $localProb }}%;">
                                @if ($localProb > 15)
                                    {{ $localProb }}%
                                @endif
                            </div>
                            <div class="bg-yellow-500 flex items-center justify-center text-[10px] text-gray-900"
                                style="width: {{ $drawProb }}%;">
                                @if ($drawProb > 5)
                                    {{ $drawProb }}%
                                @endif
                            </div>
                            <div class="bg-red-500 flex items-center justify-center text-[10px]"
                                style="width: {{ $visitorProb }}%;">
                                @if ($visitorProb > 15)
                                    {{ $visitorProb }}%
                                @endif
                            </div>
                        </div>

                        {{-- Detalle H2H --}}
                        @if ($h2hTotal > 0)
                            <p class="text-xs text-gray-400 pt-2">
                                Historial H2H: {{ $h2hTotal }} encuentros (G: {{ $h2hRecord['G'] }}, E:
                                {{ $h2hRecord['E'] }}, P: {{ $h2hRecord['P'] }})
                            </p>
                        @endif
                    </div>

                    {{-- COLUMNA 2: VOTACIÓN COMUNITARIA (Acción y Resultado Dinámico) --}}
                    <div
                        class="flex flex-col items-center justify-center border-t md:border-t-0 md:border-l border-gray-700 pt-4 md:pt-0 md:pl-4">
                        <p class="text-sm font-semibold text-gray-400 mb-2">( Reajustando sistema de votación )</p>
                        {{--
                        @if ($isVotingActive && !$hasVoted)
                            <form method="POST" action="{{ route('community.vote', $nextMatch->id) }}"
                                class="w-full max-w-sm">
                                @csrf
                                <input type="hidden" name="match_id" value="{{ $nextMatch->id }}">

                                <p class="text-sm font-semibold text-primary mb-3">¿Quién crees que ganará? (Vota)</p>
                                <div class="flex justify-center space-x-2 w-full">
                                    <button type="submit" name="voto" value="local"
                                        class="bg-primary hover:bg-green-600 px-2 py-1 rounded-full text-white text-xs flex-grow">{{ $nextMatch->localTeam->nombre }}</button>
                                    <button type="submit" name="voto" value="draw"
                                        class="bg-yellow-500 hover:bg-yellow-600 px-2 py-1 rounded-full text-gray-900 text-xs flex-shrink-0">E</button>
                                    <button type="submit" name="voto" value="visitor"
                                        class="bg-red-500 hover:bg-red-600 px-2 py-1 rounded-full text-white text-xs flex-grow">{{ $nextMatch->visitorTeam->nombre }}</button>
                                </div>
                            </form>
                        @else
                            <p class="text-sm font-semibold text-gray-400 mb-2">Voto Comunitario Actual:</p>

                            @if (!$isVotingActive)
                                <p class="text-xs text-red-400 mb-2">¡Votación cerrada!</p>
                            @endif

                            @if ($hasVoted)
                                <p class="text-xs font-semibold text-green-400 mb-2">¡Gracias por tu predicción!</p>
                            @endif

                            <div class="flex flex-col items-center w-full">

                                <div class="flex justify-between w-full max-w-xs text-xs font-semibold mb-1">
                                    <span class="text-green-400">
                                        {{ $communityLocalProb }}% ({{ $communityVotes['local'] ?? 0 }} votos)
                                    </span>
                                    <span class="text-yellow-400">
                                        {{ $communityDrawProb }}% ({{ $communityVotes['draw'] ?? 0 }} votos)
                                    </span>
                                    <span class="text-red-400">
                                        {{ $communityVisitorProb }}% ({{ $communityVotes['visitor'] ?? 0 }} votos)
                                    </span>
                                </div>

                                <div
                                    class="flex w-full max-w-xs h-3 rounded-full overflow-hidden text-xs font-bold shadow-inner mt-2">
                                    <div class="bg-green-500 flex items-center justify-center"
                                        style="width: {{ $communityLocalProb }}%;"></div>
                                    <div class="bg-yellow-500 flex items-center justify-center"
                                        style="width: {{ $communityDrawProb }}%;"></div>
                                    <div class="bg-red-500 flex items-center justify-center"
                                        style="width: {{ $communityVisitorProb }}%;"></div>
                                </div>

                                <p class="text-xs text-gray-500 mt-2">Total de votos: {{ $communityTotal ?? 0 }}</p>
                            </div>
                        @endif --}}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endif

{{-- ---------------------------------------------------- --}}
{{-- 2. TABLA DE CLASIFICACIÓN RÁPIDA (Adaptada) --}}
{{-- ---------------------------------------------------- --}}
<h3 class="text-2xl font-bold mb-4">Clasificación</h3>
<div class="bg-card-bg rounded-lg shadow-xl overflow-hidden mb-8">
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
                    <th class="py-3 px-2 text-center">Forma</th>
                </tr>
            </thead>
            <tbody id="standings-body" class="divide-y divide-gray-800 text-sm">
                @forelse($teams as $index => $team)
                    @php
                        $positionClass = '';
                        $pointsTextColor = 'text-green-400';

                        // 1er Lugar: Amarillo
                        if ($index === 0) {
                            $positionClass = 'bg-yellow-900/30 text-yellow-400 border-l-4 border-yellow-400';
                            $pointsTextColor = 'text-yellow-300';
                            // 2do Lugar: Azul Secundario
                        } elseif ($index === 1) {
                            $positionClass = 'bg-blue-900/30 text-blue-400 border-l-4 border-blue-400';
                            $pointsTextColor = 'text-blue-300';
                            // 3er Lugar en adelante: Verde Primario
                        } else {
                            $positionClass = 'bg-primary/20 text-green-400 border-l-4 border-primary';
                            $pointsTextColor = 'text-green-300';
                        }

                        $goalDiff = $team->goles_a_favor - $team->goles_en_contra;
                    @endphp

                    <tr class="hover:bg-gray-700 transition {{ $positionClass }}">
                        <td class="py-3 px-2 font-bold">{{ $index + 1 }}</td>
                        <td class="py-3 px-2">
                            <a href="{{ route('team.profile', $team->id) }}"
                                class="flex items-center hover:text-green-300 transition">
                                <img src="{{ $team->escudo_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=LOGO' }}"
                                    onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=LOGO'"
                                    class="w-8 h-8 rounded-full object-cover mr-3" />
                                <span class="font-medium text-white">{{ $team->nombre }}</span>
                            </a>
                        </td>
                        <td class="py-3 px-2 text-center font-bold {{ $pointsTextColor }}">
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
                        <td class="py-3 px-2 text-center">
                            <div class="flex space-x-0.5 justify-center">
                                @foreach (str_split($team->form_guide ?? '-----') as $result)
                                    <span class="w-2.5 h-2.5 rounded-sm"
                                        style="background-color: {{ $result === 'G' ? '#10b981' : ($result === 'E' ? '#f59e0b' : ($result === 'P' ? '#ef4444' : '#374151')) }};">
                                    </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-gray-700 transition">
                        <td colspan="11" class="py-4 text-center text-gray-500">Aún no hay equipos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ---------------------------------------------------- --}}
{{-- 1. CARDS DE ESTADÍSTICAS GENERALES --}}
{{-- ---------------------------------------------------- --}}
<div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    {{-- Equipos Participantes --}}
    <div class="bg-card-bg rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-lg">
        <span class="material-symbols-outlined text-secondary text-5xl mb-2">group</span>
        <p class="text-white/70 text-sm">Equipos</p>
        <p class="text-3xl font-bold">{{ $totalTeams }}</p>
    </div>

    {{-- Partidos Jugados --}}
    <div class="bg-card-bg rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-lg">
        <span class="material-symbols-outlined text-primary text-5xl mb-2">sports_soccer</span>
        <p class="text-white/70 text-sm">Partidos Jugados</p>
        <p class="text-3xl font-bold">{{ round($totalMatches) }}</p>
    </div>

    {{-- Goles Totales --}}
    <div class="bg-card-bg rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-lg">
        <span class="material-symbols-outlined text-red-500 text-5xl mb-2">flare</span>
        <p class="text-white/70 text-sm">Goles Totales</p>
        <p class="text-3xl font-bold">{{ $totalGoals }}</p>
    </div>

    {{-- Goles por partido --}}
    <div class="bg-card-bg rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-lg">
        <span class="material-symbols-outlined text-yellow-500 text-5xl mb-2">trending_up</span>
        <p class="text-white/70 text-sm">Goles Promedio<br>por Partido</p>
        <p class="text-3xl font-bold">{{ $avgGoals ?? 0 }}</p>
    </div>

    <div class="bg-card-bg rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-lg">
        <span class="material-symbols-outlined text-purple-400 text-5xl mb-2">star_rate</span>
        <p class="text-white/70 text-sm">Jugador Más Influyente</p>

        @if ($topImpactPlayer)
            {{-- ⬇️ CORRECCIÓN: Envolver la información en el enlace ⬇️ --}}
            <a href="{{ route('player.profile', $topImpactPlayer->id) }}"
                class="block hover:opacity-80 transition duration-150">

                <p class="text-xl font-bold">{{ $topImpactPlayer->nombre }}</p>
                <p class="text-white/70 text-sm">{{ $topImpactPlayer->goles + $topImpactPlayer->asistencias }} Puntos
                    de
                    Impacto</p>

            </a>
        @else
            <p class="text-xl font-bold text-white/50">N/A</p>
        @endif
    </div>

    <div class="bg-card-bg rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-lg">
        <span class="material-symbols-outlined text-blue-400 text-5xl mb-2">handshake</span>
        <p class="text-white/70 text-sm">Equipo Más Limpio</p>
        @php
            // Esto asume que discipline_points fue calculado en el controlador
            $cleanestTeam = $teams->sortBy('discipline_points')->first();
        @endphp
        @if ($cleanestTeam)
            <p class="text-xl font-bold">{{ $cleanestTeam->nombre }}</p>
            <p class="text-white/50 text-xs">{{ $cleanestTeam->discipline_points }} Pts. Disciplina</p>
        @endif
    </div>

    <div class="bg-card-bg rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-lg">
        <span class="material-symbols-outlined text-primary text-5xl mb-2">shield</span>
        <p class="text-white/70 text-sm">Muro Defensivo</p>
        @if ($bestDefenseTeam)
            <p class="text-xl font-bold">{{ $bestDefenseTeam->nombre }}</p>
            <p class="text-white/70 text-xs">{{ $bestDefenseTeam->goles_en_contra }} Goles Recibidos</p>
        @else
            <p class="text-xl font-bold text-white/50">N/A</p>
        @endif
    </div>

    <div class="bg-card-bg rounded-lg p-6 flex flex-col items-center justify-center text-center shadow-lg">
        <span class="material-symbols-outlined text-red-500 text-5xl mb-2">rocket_launch</span>
        <p class="text-white/70 text-sm">Ataque Más Potente</p>
        @if ($mostOffensiveTeam)
            <p class="text-xl font-bold">{{ $mostOffensiveTeam->nombre }}</p>
            <p class="text-white/70 text-xs">{{ $mostOffensiveTeam->goles_a_favor }} Goles a Favor</p>
        @else
            <p class="text-xl font-bold text-white/50">N/A</p>
        @endif
    </div>
</div>

{{-- ---------------------------------------------------- --}}
{{-- 3. ANÁLISIS RÁPIDO DE EQUIPOS (Top 4) --}}
{{-- ---------------------------------------------------- --}}
<h3 class="text-2xl font-bold mb-4">Análisis Rápido de Equipos</h3>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($topTeams as $index => $team)
        @php
            $rank = $index + 1;
            $gf = $team->goles_a_favor;
            $gc = $team->goles_en_contra;
            $totalGoals = $gf + $gc;
            $offensiveRatio = $totalGoals > 0 ? ($gf / $totalGoals) * 100 : 0;
            $coneDegree = $offensiveRatio * 3.6;
            $diffSign = $gf - $gc >= 0 ? 'text-green-400' : 'text-red-400';
            $goalDiff = $gf - $gc;
            $winRatio = $team->partidos_jugados > 0 ? ($team->ganados / $team->partidos_jugados) * 100 : 0;
            $winWidth = number_format($winRatio, 0);
            $rankClass = $rank === 1 ? 'bg-yellow-400 text-gray-900' : 'bg-gray-600 text-white';
        @endphp

        <a href="{{ route('team.profile', $team->id) }}" class="block">
            <div
                class="bg-card-bg rounded-lg p-5 flex flex-col shadow-xl border border-primary/20 
                        transform hover:scale-[1.03] transition duration-200 relative overflow-hidden">

                {{-- ⬇️ 1. CABECERA PRINCIPAL (Nombre, Logo y Rank) ⬇️ --}}
                <div class="flex items-center justify-between w-full border-b border-gray-700 pb-3 mb-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $team->escudo_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=L' }}"
                            onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=L'"
                            class="w-10 h-10 rounded-full object-cover border-2 {{ $rank === 1 ? 'border-yellow-400' : 'border-primary/50' }}">

                        <h4 class="text-xl font-extrabold text-white">{{ $team->nombre }}</h4>
                    </div>

                    {{-- Rank Badge --}}
                    <span class="text-xs font-bold px-3 py-1 rounded-full {{ $rankClass }}">
                        #{{ $rank }}
                    </span>
                </div>

                {{-- ⬇️ 2. CONTENIDO PRINCIPAL: DIVISION EN DOS COLUMNAS ⬇️ --}}
                <div class="grid grid-cols-2 gap-4 w-full">

                    {{-- COLUMNA IZQUIERDA: RENDIMIENTO DETALLADO (Datos duros) --}}
                    <div class="space-y-3">

                        {{-- Métrica: PUNTOS --}}
                        <div class="text-left">
                            <p class="text-white/70 text-sm">Puntos</p>
                            <p class="text-2xl font-bold text-primary">{{ $team->puntos }}</p>
                        </div>

                        {{-- Métrica: DIFERENCIA --}}
                        <div class="text-left">
                            <p class="text-white/70 text-sm">DIF</p>
                            <p class="text-2xl font-bold {{ $diffSign }}">
                                {{ $goalDiff > 0 ? '+' : '' }}{{ $goalDiff }}</p>
                        </div>

                        {{-- Métrica: Partidos Jugados --}}
                        <div class="text-left">
                            <p class="text-white/70 text-sm">PJ</p>
                            <p class="text-xl font-bold">{{ $team->partidos_jugados }}</p>
                        </div>
                    </div>

                    {{-- ⬇️ COLUMNA DERECHA: GRÁFICOS Y ANÁLISIS (Agrupación Visual) ⬇️ --}}
                    <div class="space-y-4 text-right flex flex-col justify-between items-end">

                        {{-- GRÁFICO OFENSIVO/DEFENSIVO --}}
                        <div class="flex flex-col items-end w-full">

                            {{-- ⬇️ LEYENDA CLARA Y CONCISA ⬇️ --}}
                            <div class="flex text-[10px] font-semibold space-x-2 text-gray-400 mb-1">
                                <span class="flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span> Ataque
                                </span>
                                <span class="flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-red-500 mr-1"></span> Defensa
                                </span>
                            </div>

                            {{-- Gráfica de Pastel --}}
                            <div class="pie-chart flex items-center justify-center flex-shrink-0"
                                style="background: conic-gradient(#ef4444 0deg, #ef4444 {{ 360 - $coneDegree }}deg, #10b981 {{ 360 - $coneDegree }}deg, #10b981 360deg); width: 60px; height: 60px;">
                                <span
                                    class="text-xs font-bold text-white z-20">{{ number_format($offensiveRatio, 0) }}%</span>
                            </div>
                        </div>

                        {{-- Barra de Progreso (Ratio de Victoria) --}}
                        <div class="w-full mt-auto">
                            <p class="text-xs font-semibold text-gray-400 mb-1">Ratio Victoria: <span
                                    class="text-green-500">{{ number_format($winRatio, 0) }}%</span></p>
                            <div class="w-full h-2 rounded-full bg-red-600 overflow-hidden">
                                <div class="h-full rounded-full bg-green-500" style="width: {{ $winWidth }}%;">
                                </div>
                            </div>
                        </div>

                        {{-- Guía de Forma --}}
                        <div class="w-full pt-1">
                            <p class="text-xs font-semibold text-gray-400 mb-1">Última Forma</p>
                            <div class="flex space-x-1 justify-end">
                                @foreach (str_split($team->form_guide ?? '-----') as $result)
                                    <span
                                        class="w-4 h-4 flex items-center justify-center rounded-sm text-xs font-bold text-white/90"
                                        style="background-color: {{ $result === 'G' ? '#10b981' : ($result === 'E' ? '#f59e0b' : ($result === 'P' ? '#ef4444' : '#374151')) }};">
                                        {{ $result }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <p class="text-white/50 p-6">Aún no hay suficientes equipos para mostrar un detalle destacado.</p>
    @endforelse
</div>
{{-- ========================================================== --}}
{{-- 4. CARD MEJORADA: PARTE MÉDICO DE LA LIGA (DINÁMICA)       --}}
{{-- ========================================================== --}}
<div class="mt-8">

    @if ($injuredPlayers->isNotEmpty())
        {{-- ESTADO 1: HAY JUGADORES LESIONADOS --}}
        <h3 class="text-2xl font-bold mb-4 flex items-center gap-3">
            <span class="text-red-500">
                {{-- ✅ TAMAÑO AUMENTADO: Cambiamos h-4 w-4 por h-7 w-7 --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
            </span>
            <span>Jugadores Lesionados</span>
        </h3>
        <div class="bg-card-bg rounded-lg shadow-xl p-4 border border-red-500/30">
            {{-- ✨ MEJORA: Se convierte en una cuadrícula para mejor visualización --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[450px] overflow-y-auto pr-2">
                @foreach ($injuredPlayers as $player)
                    <a href="{{ route('player.profile', $player->id) }}"
                        class="block bg-gray-700/50 p-3 rounded-lg hover:bg-gray-700 hover:scale-105 transition-transform duration-200 group">
                        <div class="flex items-center">
                            <div class="relative flex-shrink-0">
                                <img src="{{ $player->foto_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=JUG' }}"
                                    class="w-14 h-14 rounded-full object-cover border-2 border-gray-600 group-hover:border-red-500 transition" />
                                {{-- ✨ MEJORA: Icono de lesión más descriptivo --}}
                                <div
                                    class="absolute -bottom-1 -right-1 bg-red-600 rounded-full p-1 border-2 border-gray-800">
                                    <i class="fa-solid fa-briefcase-medical text-white text-xs"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="font-bold text-white transition">{{ $player->nombre }}</p>
                                <p class="text-sm text-gray-400">{{ $player->equipo->nombre ?? 'Sin Equipo' }}</p>
                                {{-- ✨ MEJORA: Se añade la posición para más contexto --}}
                                <span
                                    class="mt-1 inline-block bg-gray-600 text-gray-300 text-xs font-bold px-2 py-0.5 rounded-full">
                                    {{ strtoupper($player->posicion_especifica) }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        {{-- ESTADO 2: NO HAY JUGADORES LESIONADOS (MENSAJE DIVERTIDO) --}}
        <h3 class="text-2xl font-bold mb-4 flex items-center gap-3">
            <i class="fa-solid fa-shield-heart text-green-500"></i>
            <span>Estado de la Plantilla</span>
        </h3>
        <div class="bg-card-bg rounded-lg shadow-xl p-8 border border-green-500/30 text-center">
            <div class="flex flex-col items-center">
                <i class="fa-solid fa-shield-heart text-green-400 text-6xl mb-4 animate-pulse"></i>
                <h4 class="text-xl font-bold text-white">¡Enfermería Vacía!</h4>
                <p class="text-gray-400 mt-2">
                    ¡Buenas noticias! El fisio está de vacaciones. <br class="hidden sm:block">
                    Todos los jugadores están en plena forma y listos para jugar.
                </p>
            </div>
        </div>
    @endif
</div>
