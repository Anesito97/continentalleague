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

{{-- MEJORA: Título con gradiente --}}
<h2 class="text-3xl font-bold mb-6 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
    Resumen de la Liga
</h2>

@if ($nextMatch)
    <!-- @php
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
            @endphp -->

    {{-- ---------------------------------------------------- --}}
    {{-- SLIDER "MODERNO" CON BARRA CLÁSICA DE PROBABILIDAD --}}
    {{-- ---------------------------------------------------- --}}

    <div class="relative w-full max-w-5xl mx-auto py-8">

        {{-- Título con estilo neón sutil --}}
        <div class="flex items-center justify-between mb-6 px-4">
            <h3 class="text-2xl font-bold text-white drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]">
                <span class="text-primary">Próximos</span> Grandes Duelos
            </h3>

            {{-- Indicador visual de "Deslizar" (Solo móvil) --}}
            <div class="flex items-center gap-1 text-xs text-gray-400 sm:hidden animate-pulse">
                <span>Desliza</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </div>
        </div>

        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="relative group">

            {{-- Botón Anterior (Flotante y con Blur) --}}
            <button id="btn-prev"
                class="absolute left-2 top-1/2 -translate-y-1/2 z-20 bg-black/40 backdrop-blur-md border border-white/10 text-white p-3 rounded-full hover:bg-primary hover:border-primary transition-all duration-300 transform hover:scale-110 opacity-0 group-hover:opacity-100 hidden sm:flex items-center justify-center shadow-lg shadow-black/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- SLIDER (Scroll Snap con Padding para efecto "Peek") --}}
            <div id="modern-slider"
                class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth pb-8 pt-2 no-scrollbar px-4 sm:px-0 gap-4"
                style="scroll-padding-left: 1rem;">

                @foreach($sliderMatches as $index => $item)
                    @php
                        $nextMatch = $item->match;
                        $localProb = $item->prediction['localProb'];
                        $drawProb = $item->prediction['drawProb'];
                        $visitorProb = $item->prediction['visitorProb'];
                        $probTitle = $item->probTitle;
                        $h2hRecord = $item->h2hRecord;
                        $h2hTotal = $h2hRecord['total'];
                    @endphp

                    {{-- ITEM INDIVIDUAL --}}
                    {{-- w-[90%] permite ver el 10% de la siguiente tarjeta en móviles --}}
                    <div class="slider-item snap-center shrink-0 w-[90%] sm:w-[600px] md:w-[700px] transition-all duration-500 ease-out opacity-50 scale-95"
                        data-index="{{ $index }}">

                        {{-- Etiqueta Flotante de Jornada --}}
                        <div class="text-center -mb-3 relative z-10">
                            <span
                                class="bg-gray-900/90 border border-gray-700 text-gray-300 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest shadow-lg">
                                Jornada {{ $nextMatch->jornada }}
                            </span>
                        </div>

                        {{-- TARJETA GLASSSMORPHISM --}}
                        <div
                            class="card relative overflow-hidden bg-[#1a1f2e]/90 backdrop-blur-xl border border-white/10 w-full p-4 sm:p-6 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.5)] rounded-2xl group-hover/card:border-primary/30 transition-all">

                            {{-- Efecto de brillo de fondo (Glow) --}}
                            <div
                                class="absolute top-0 right-0 -mr-16 -mt-16 w-32 h-32 bg-primary/20 blur-[60px] rounded-full pointer-events-none">
                            </div>
                            <div
                                class="absolute bottom-0 left-0 -ml-16 -mb-16 w-32 h-32 bg-secondary/20 blur-[60px] rounded-full pointer-events-none">
                            </div>

                            {{-- CONTENIDO DE LA TARJETA --}}
                            <div class="relative z-10">
                                {{-- 1. CABECERA EQUIPOS --}}
                                <div class="flex flex-row items-start justify-between space-x-2">
                                    {{-- LOCAL --}}
                                    <div class="flex flex-col items-center w-5/12 text-center flex-shrink-0">
                                        <a href="{{ route('team.profile', $nextMatch->localTeam->id) }}"
                                            class="flex flex-col items-center hover:scale-105 transition transform duration-300">
                                            <div class="relative">
                                                <div class="absolute inset-0 bg-primary/30 blur-md rounded-full"></div>
                                                <img src="{{ $nextMatch->localTeam->escudo_url ?? 'https://placehold.co/100x100' }}"
                                                    alt="Local"
                                                    class="relative w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-2 border-primary/80 shadow-lg z-10" />
                                            </div>
                                            <span
                                                class="mt-2 text-sm sm:text-lg font-bold text-white leading-tight">{{ $nextMatch->localTeam->nombre }}</span>
                                        </a>
                                        <!-- Possible Lineup Button -->
                                        <a href="{{ route('ideal-eleven', ['team_id' => $nextMatch->localTeam->id]) }}"
                                            class="mt-2 px-2 py-1 bg-primary/20 hover:bg-primary/40 border border-primary/50 rounded text-[10px] text-primary hover:text-white transition font-bold uppercase tracking-wider">
                                            Posible Alineación
                                        </a>
                                    </div>

                                    {{-- VS --}}
                                    <div class="w-2/12 flex flex-col items-center justify-center pt-4">
                                        <span
                                            class="text-3xl sm:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-b from-red-500 to-red-700 drop-shadow-sm italic">VS</span>
                                    </div>

                                    {{-- VISITANTE --}}
                                    <div class="flex flex-col items-center w-5/12 text-center flex-shrink-0">
                                        <a href="{{ route('team.profile', $nextMatch->visitorTeam->id) }}"
                                            class="flex flex-col items-center hover:scale-105 transition transform duration-300">
                                            <div class="relative">
                                                <div class="absolute inset-0 bg-secondary/30 blur-md rounded-full"></div>
                                                <img src="{{ $nextMatch->visitorTeam->escudo_url ?? 'https://placehold.co/100x100' }}"
                                                    alt="Visitante"
                                                    class="relative w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-2 border-secondary/80 shadow-lg z-10" />
                                            </div>
                                            <span
                                                class="mt-2 text-sm sm:text-lg font-bold text-white leading-tight">{{ $nextMatch->visitorTeam->nombre }}</span>
                                        </a>
                                        <!-- Possible Lineup Button -->
                                        <a href="{{ route('ideal-eleven', ['team_id' => $nextMatch->visitorTeam->id]) }}"
                                            class="mt-2 px-2 py-1 bg-secondary/20 hover:bg-secondary/40 border border-secondary/50 rounded text-[10px] text-secondary hover:text-white transition font-bold uppercase tracking-wider">
                                            Posible Alineación
                                        </a>
                                    </div>
                                </div>

                                {{-- 2. DATOS CON BARRA UNIFICADA (Aquí está la corrección) --}}
                                <div class="mt-5 pt-4 border-t border-white/5">

                                    {{-- Fecha y Hora --}}
                                    <div class="text-center mb-5">
                                        <span class="text-base text-gray-200 font-medium">
                                            {{ \Carbon\Carbon::parse($nextMatch->fecha_hora)->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                                        </span><br>
                                        <span class="text-base text-primary font-bold">
                                            {{ \Carbon\Carbon::parse($nextMatch->fecha_hora)->format('h:i A') }}
                                        </span>
                                    </div>

                                    {{-- ⬇️ AQUÍ ESTÁ EL CAMBIO: BARRA DE PROBABILIDAD UNIFICADA ⬇️ --}}
                                    <div class="w-full max-w-lg mx-auto px-2">
                                        <span
                                            class="text-xs font-semibold text-gray-400 block mb-2 text-center uppercase tracking-wider">
                                            {{ $probTitle }}
                                        </span>

                                        {{-- La Barra: Flex container con 3 colores --}}
                                        <div
                                            class="flex w-full h-5 rounded-full overflow-hidden text-[10px] sm:text-xs font-bold shadow-inner border border-white/10">

                                            {{-- 1. LOCAL (Verde) --}}
                                            <div class="bg-green-600 flex items-center justify-center text-white relative group/segment"
                                                style="width: {{ $localProb }}%;">
                                                @if ($localProb > 10)
                                                    <span>{{ $localProb }}%</span>
                                                @endif
                                                <div
                                                    class="absolute -top-8 bg-gray-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover/segment:opacity-100 transition z-20">
                                                    Victoria Local</div>
                                            </div>

                                            {{-- 2. EMPATE (Amarillo) --}}
                                            <div class="bg-yellow-500 flex items-center justify-center text-gray-900 relative group/segment"
                                                style="width: {{ $drawProb }}%;">
                                                @if ($drawProb > 5)
                                                    <span>{{ $drawProb }}%</span>
                                                @endif
                                                <div
                                                    class="absolute -top-8 bg-gray-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover/segment:opacity-100 transition z-20">
                                                    Empate</div>
                                            </div>

                                            {{-- 3. VISITANTE (Rojo) --}}
                                            <div class="bg-red-600 flex items-center justify-center text-white relative group/segment"
                                                style="width: {{ $visitorProb }}%;">
                                                @if ($visitorProb > 10)
                                                    <span>{{ $visitorProb }}%</span>
                                                @endif
                                                <div
                                                    class="absolute -top-8 bg-gray-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover/segment:opacity-100 transition z-20">
                                                    Victoria Visitante</div>
                                            </div>
                                        </div>

                                        {{-- Texto H2H Debajo de la barra --}}
                                        @if ($h2hTotal > 0)
                                            <p class="text-[10px] sm:text-xs text-gray-400 mt-3 text-center">
                                                Historial: <span class="text-gray-300">{{ $h2hTotal }}</span> encuentros
                                                (L: <span class="text-green-400">{{ $h2hRecord['G'] }}</span> -
                                                E: <span class="text-yellow-400">{{ $h2hRecord['E'] }}</span> -
                                                V: <span class="text-red-400">{{ $h2hRecord['P'] }}</span>)
                                            </p>
                                        @endif
                                    </div>

                                    {{-- ⬇️ VOTACIÓN DE LA COMUNIDAD ⬇️ --}}
                                    <div class="mt-4 px-4">
                                        @if(Auth::check())
                                            @if($item->hasVoted)
                                                {{-- YA VOTÓ: Mostrar resultados --}}
                                                <div class="text-center">
                                                    <p class="text-xs text-gray-400 mb-2">
                                                        Tu voto: <span
                                                            class="font-bold text-primary uppercase">{{ $item->userVote === 'draw' ? 'Empate' : ($item->userVote === 'local' ? 'Local' : 'Visitante') }}</span>
                                                    </p>

                                                    {{-- Barra de Resultados de la Comunidad --}}
                                                    @php
                                                        $total = $item->totalVotes > 0 ? $item->totalVotes : 1;
                                                        $pLocal = round(($item->votes['local'] / $total) * 100);
                                                        $pDraw = round(($item->votes['draw'] / $total) * 100);
                                                        $pVisitor = round(($item->votes['visitor'] / $total) * 100);
                                                    @endphp

                                                    <div class="w-full h-2 bg-gray-700 rounded-full overflow-hidden flex">
                                                        <div class="bg-green-500 h-full" style="width: {{ $pLocal }}%"></div>
                                                        <div class="bg-yellow-500 h-full" style="width: {{ $pDraw }}%"></div>
                                                        <div class="bg-red-500 h-full" style="width: {{ $pVisitor }}%"></div>
                                                    </div>
                                                    <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                                                        <span>{{ $pLocal }}%</span>
                                                        <span>{{ $pDraw }}%</span>
                                                        <span>{{ $pVisitor }}%</span>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- NO HA VOTADO: Mostrar botones --}}
                                                <form action="{{ route('community.vote', $nextMatch->id) }}" method="POST"
                                                    class="flex justify-center gap-2">
                                                    @csrf
                                                    <button type="submit" name="voto" value="local"
                                                        class="px-3 py-1 bg-green-600/20 hover:bg-green-600 text-green-400 hover:text-white border border-green-600 rounded text-xs font-bold transition">
                                                        Local
                                                    </button>
                                                    <button type="submit" name="voto" value="draw"
                                                        class="px-3 py-1 bg-yellow-600/20 hover:bg-yellow-600 text-yellow-400 hover:text-white border border-yellow-600 rounded text-xs font-bold transition">
                                                        Empate
                                                    </button>
                                                    <button type="submit" name="voto" value="visitor"
                                                        class="px-3 py-1 bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white border border-red-600 rounded text-xs font-bold transition">
                                                        Visitante
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            {{-- NO LOGUEADO: Botón para iniciar sesión --}}
                                            <div class="text-center">
                                                <button
                                                    onclick="document.getElementById('login-modal').classList.remove('hidden'); document.getElementById('login-modal').classList.add('flex');"
                                                    class="text-xs text-primary hover:text-white underline decoration-dashed underline-offset-4 transition">
                                                    Inicia sesión para votar
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- ⬆️ FIN DEL CAMBIO ⬆️ --}}

                                </div>
                            </div>
                        </div>
                        {{-- FIN TARJETA --}}
                    </div>
                @endforeach

                {{-- ELEMENTO FANTASMA AL FINAL --}}
                <div class="shrink-0 w-4 sm:w-0"></div>

            </div>

            {{-- Botón Siguiente (Flotante) --}}
            <button id="btn-next"
                class="absolute right-2 top-1/2 -translate-y-1/2 z-20 bg-black/40 backdrop-blur-md border border-white/10 text-white p-3 rounded-full hover:bg-primary hover:border-primary transition-all duration-300 transform hover:scale-110 opacity-0 group-hover:opacity-100 hidden sm:flex items-center justify-center shadow-lg shadow-black/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        {{-- PAGINACIÓN / INDICADORES --}}
        <div class="flex justify-center mt-2 space-x-2" id="slider-dots">
            @foreach($sliderMatches as $index => $item)
                <button
                    class="dot transition-all duration-300 h-1.5 rounded-full bg-gray-600 hover:bg-gray-400 {{ $index === 0 ? 'w-8 bg-primary' : 'w-2' }}"
                    onclick="scrollToSlide({{ $index }})">
                </button>
            @endforeach
        </div>

    </div>

    {{-- ESTILOS NECESARIOS --}}
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Clase para cuando el item está activo (enfocado) */
        .slider-active {
            opacity: 1 !important;
            transform: scale(1) !important;
            z-index: 10;
        }
    </style>
@endif

{{-- ---------------------------------------------------- --}}
{{-- 2. TABLA DE CLASIFICACIÓN RÁPIDA (Adaptada) --}}
{{-- ---------------------------------------------------- --}}
<h3 class="text-2xl font-bold mb-4">Clasificación</h3>
{{-- MEJORA: "Glassmorphism" para la tabla --}}
<div class="bg-card-bg/80 backdrop-blur-lg rounded-lg shadow-xl overflow-hidden mb-8 border border-white/10">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/5">
            <thead>
                {{-- MEJORA: Header de tabla translúcido --}}
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 bg-gray-900/50">
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
            <tbody id="standings-body" class="divide-y divide-white/5 text-sm">
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

                    {{-- MEJORA: Hover más sutil --}}
                    <tr class="hover:bg-white/10 transition {{ $positionClass }}">
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
                            {{ $team->puntos }}
                        </td>
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

    {{-- MEJORA: Aplicada la clase .card global --}}
    <div class="card p-6 flex flex-col items-center justify-center text-center">
        <span class="material-symbols-outlined text-secondary text-5xl mb-2">group</span>
        <p class="text-white/70 text-sm">Equipos</p>
        <p class="text-3xl font-bold">{{ $totalTeams }}</p>
    </div>

    {{-- MEJORA: Aplicada la clase .card global --}}
    <div class="card p-6 flex flex-col items-center justify-center text-center">
        <span class="material-symbols-outlined text-primary text-5xl mb-2">sports_soccer</span>
        <p class="text-white/70 text-sm">Partidos Jugados</p>
        <p class="text-3xl font-bold">{{ round($totalMatches) }}</p>
    </div>

    {{-- MEJORA: Aplicada la clase .card global --}}
    <div class="card p-6 flex flex-col items-center justify-center text-center">
        <span class="material-symbols-outlined text-red-500 text-5xl mb-2">flare</span>
        <p class="text-white/70 text-sm">Goles Totales</p>
        <p class="text-3xl font-bold">{{ $totalGoals }}</p>
    </div>

    {{-- MEJORA: Aplicada la clase .card global --}}
    <div class="card p-6 flex flex-col items-center justify-center text-center">
        <span class="material-symbols-outlined text-yellow-500 text-5xl mb-2">trending_up</span>
        <p class="text-white/70 text-sm">Goles Promedio<br>por Partido</p>
        <p class="text-3xl font-bold">{{ $avgGoals ?? 0 }}</p>
    </div>

    {{-- MEJORA: Aplicada la clase .card global --}}
    <div class="card p-6 flex flex-col items-center justify-center text-center">
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

    {{-- MEJORA: Aplicada la clase .card global --}}
    <div class="card p-6 flex flex-col items-center justify-center text-center">
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

    {{-- MEJORA: Aplicada la clase .card global --}}
    <div class="card p-6 flex flex-col items-center justify-center text-center">
        <span class="material-symbols-outlined text-primary text-5xl mb-2">shield</span>
        <p class="text-white/70 text-sm">Muro Defensivo</p>
        @if ($bestDefenseTeam)
            <p class="text-xl font-bold">{{ $bestDefenseTeam->nombre }}</p>
            <p class="text-white/70 text-xs">{{ $bestDefenseTeam->goles_en_contra }} Goles Recibidos</p>
        @else
            <p class="text-xl font-bold text-white/50">N/A</p>
        @endif
    </div>

    {{-- MEJORA: Aplicada la clase .card global --}}
    <div class="card p-6 flex flex-col items-center justify-center text-center">
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
            {{-- MEJORA: "Glassmorphism" para las tarjetas de análisis --}}
            <div class="bg-card-bg/80 backdrop-blur-lg rounded-lg p-5 flex flex-col shadow-xl border border-primary/20 
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
                                {{ $goalDiff > 0 ? '+' : '' }}{{ $goalDiff }}
                            </p>
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
{{-- 4. CARD MEJORADA: PARTE MÉDICO DE LA LIGA (DINÁMICA) --}}
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
        {{-- MEJORA: "Glassmorphism" para la tarjeta de lesionados --}}
        <div class="bg-card-bg/80 backdrop-blur-lg rounded-lg shadow-xl p-4 border border-red-500/30">
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
                                <div class="absolute -bottom-1 -right-1 bg-red-600 rounded-full p-1 border-2 border-gray-800">
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
        {{-- MEJORA: "Glassmorphism" para la tarjeta de "enfermería vacía" --}}
        <div class="bg-card-bg/80 backdrop-blur-lg rounded-lg shadow-xl p-8 border border-green-500/30 text-center">
            <div class="flex flex-col items-center">
                <i class="fa-solid fa-shield-heart text-green-400 text-6xl mb-4 animate-pulse"></i>
                <h4 class="text-xl font-bold text-white">¡Enfermería Vacía!</h4>
                <p class="text-gray-400 mt-2">
                    ¡Buenas noticias! El fisio está de vacaciones. <br class="hidden sm:block">
                    Todos los jugadores están en plena forma y listos para jugar.
                    </abp>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slider = document.getElementById('modern-slider');
        const items = document.querySelectorAll('.slider-item');
        const dots = document.querySelectorAll('.dot');
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');

        // Configuración para detectar qué tarjeta está centrada
        const observerOptions = {
            root: slider,
            threshold: 0.5 // Se activa cuando el 50% de la tarjeta es visible
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Quitamos estilo activo a todos
                    items.forEach(i => {
                        i.classList.remove('slider-active');
                        i.classList.add('opacity-50', 'scale-95');
                    });

                    // Activamos el actual (Efecto POP)
                    entry.target.classList.add('slider-active');
                    entry.target.classList.remove('opacity-50', 'scale-95');

                    // Actualizamos los puntos de abajo
                    const index = entry.target.getAttribute('data-index');
                    updateDots(index);
                }
            });
        }, observerOptions);

        items.forEach(item => observer.observe(item));

        // Función para actualizar los puntos (Dots)
        function updateDots(activeIndex) {
            dots.forEach((dot, idx) => {
                if (idx == activeIndex) {
                    dot.classList.remove('w-2', 'bg-gray-600');
                    dot.classList.add('w-8', 'bg-primary'); // Alargar el activo
                } else {
                    dot.classList.add('w-2', 'bg-gray-600');
                    dot.classList.remove('w-8', 'bg-primary');
                }
            });
        }

        // Lógica de Botones
        btnPrev.addEventListener('click', () => {
            slider.scrollBy({ left: -300, behavior: 'smooth' });
        });

        btnNext.addEventListener('click', () => {
            slider.scrollBy({ left: 300, behavior: 'smooth' });
        });

        // Función global para click en los dots
        window.scrollToSlide = (index) => {
            const target = items[index];
            if (target) {
                // Cálculo simple para centrar el elemento
                const left = target.offsetLeft - (slider.clientWidth - target.clientWidth) / 2;
                slider.scrollTo({ left: left, behavior: 'smooth' });
            }
        };
    });
</script>