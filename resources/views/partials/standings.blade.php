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
        $localProb = $h2hTotal > 0 ? number_format(($h2hLocalWins / $h2hTotal) * 100, 0) : 50;
        $visitorProb = $h2hTotal > 0 ? number_format(($h2hVisitorWins / $h2hTotal) * 100, 0) : 50;
        $drawProb = 100 - ($localProb + $visitorProb);

        // Si no hay historial, usamos el 50/50 y lo etiquetamos como "Basado en Racha"
        $probTitle = $h2hTotal > 0 ? 'Probabilidad (Basado en H2H)' : 'Probabilidad (Estimada)';
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

                    {{-- VS (Elemento de Alto Impacto) --}}
                    <div
                        class="py-1 mx-auto max-w-full">
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

                {{-- Fecha y Hora (Movidas desde el centro y estilizadas) --}}
                <span class="text-lg font-semibold text-white block mb-3">
                    {{ \Carbon\Carbon::parse($nextMatch->fecha_hora)->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                    <span class="text-gray-400 font-normal">a las</span>
                    {{ \Carbon\Carbon::parse($nextMatch->fecha_hora)->format('h:i A') }}
                </span>

                {{-- Probabilidad y Barra --}}
                <span class="text-sm font-semibold text-gray-400 block mb-2">{{ $probTitle }}</span>

                <div class="flex w-3/4 max-w-md h-3 rounded-full overflow-hidden text-xs font-bold mx-auto">
                    {{-- Local Win (Verde) --}}
                    <div class="bg-green-500 flex items-center justify-center" style="width: {{ $localProb }}%;">
                        @if ($localProb > 15)
                            <span class="text-[10px]">{{ $localProb }}%</span>
                        @endif
                    </div>
                    {{-- Draw (Amarillo) --}}
                    <div class="bg-yellow-500 flex items-center justify-center" style="width: {{ $drawProb }}%;">
                        @if ($drawProb > 5)
                            <span class="text-[10px]">{{ $drawProb }}%</span>
                        @endif
                    </div>
                    {{-- Visitor Win (Rojo) --}}
                    <div class="bg-red-500 flex items-center justify-center" style="width: {{ $visitorProb }}%;">
                        @if ($visitorProb > 15)
                            <span class="text-[10px]">{{ $visitorProb }}%</span>
                        @endif
                    </div>
                </div>

                {{-- Detalle H2H --}}
                @if ($h2hTotal > 0)
                    <p class="text-xs text-gray-400 pt-3">
                        Historial H2H: {{ $h2hTotal }} encuentros (G: {{ $h2hRecord['G'] }}, E:
                        {{ $h2hRecord['E'] }}, P: {{ $h2hRecord['P'] }})
                    </p>
                @endif
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
