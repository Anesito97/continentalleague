@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">

        {{-- 1. HEADER DEL ENFRENTAMIENTO --}}
        <div class="bg-card-bg border border-white/10 rounded-xl p-6 mb-6 relative overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/10 via-transparent to-red-500/10 opacity-40"></div>

            <div class="flex items-center justify-between relative z-10">
                {{-- MI EQUIPO --}}
                <div class="flex items-center gap-4 w-1/3">
                    <img src="{{ $myTeam->escudo_url ?? 'https://placehold.co/100x100' }}"
                        class="w-20 h-20 object-contain drop-shadow-lg">
                    <div>
                        <h2 class="text-3xl font-black text-white leading-none">{{ $myTeam->nombre }}</h2>
                        <span class="text-primary font-bold text-xs uppercase tracking-widest">Tu Equipo</span>
                    </div>
                </div>

                {{-- PROBABILIDAD DE VICTORIA (GAUGE) --}}
                <div class="flex flex-col items-center justify-center w-1/3">
                    <div
                        class="relative w-full max-w-[200px] h-4 bg-gray-800 rounded-full overflow-hidden border border-white/20">
                        <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-primary to-green-400 transition-all duration-1000"
                            style="width: {{ $winProb }}%"></div>
                    </div>
                    <div class="flex justify-between w-full max-w-[200px] mt-1 text-xs font-bold">
                        <span class="text-primary">{{ $winProb }}% Victoria</span>
                        <span class="text-red-500">{{ 100 - $winProb }}% Derrota/Empate</span>
                    </div>
                    <span class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest">Probabilidad IA</span>
                </div>

                {{-- RIVAL --}}
                <div class="flex items-center gap-4 w-1/3 justify-end text-right">
                    <div>
                        <h2 class="text-3xl font-black text-white leading-none">{{ $opponent->nombre }}</h2>
                        <span class="text-red-500 font-bold text-xs uppercase tracking-widest">El Rival</span>
                    </div>
                    <img src="{{ $opponent->escudo_url ?? 'https://placehold.co/100x100' }}"
                        class="w-20 h-20 object-contain drop-shadow-lg">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- COLUMNA IZQUIERDA: DATOS DUROS --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- TALE OF THE TAPE --}}
                <div class="bg-card-bg/90 backdrop-blur-md border border-white/10 rounded-xl overflow-hidden">
                    <div class="bg-black/20 p-4 border-b border-white/5 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-scale-balanced text-yellow-500"></i>
                            Comparativa Directa (Tale of the Tape)
                        </h3>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-white/5 text-gray-400 text-xs uppercase">
                            <tr>
                                <th class="py-3 px-4 text-left w-1/3">{{ $myTeam->nombre }}</th>
                                <th class="py-3 px-4 text-center w-1/3">Métrica</th>
                                <th class="py-3 px-4 text-right w-1/3">{{ $opponent->nombre }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            {{-- Puntos por Partido --}}
                            <tr class="hover:bg-white/5 transition">
                                <td
                                    class="py-3 px-4 font-bold {{ $metrics['my']['ppg'] > $metrics['op']['ppg'] ? 'text-green-400' : '' }}">
                                    {{ $metrics['my']['ppg'] }}</td>
                                <td class="py-3 px-4 text-center text-gray-500">Puntos x Partido</td>
                                <td
                                    class="py-3 px-4 text-right font-bold {{ $metrics['op']['ppg'] > $metrics['my']['ppg'] ? 'text-green-400' : '' }}">
                                    {{ $metrics['op']['ppg'] }}</td>
                            </tr>
                            {{-- Goles a Favor --}}
                            <tr class="hover:bg-white/5 transition">
                                <td
                                    class="py-3 px-4 font-bold {{ $metrics['my']['gf_pg'] > $metrics['op']['gf_pg'] ? 'text-green-400' : '' }}">
                                    {{ $metrics['my']['gf_pg'] }}</td>
                                <td class="py-3 px-4 text-center text-gray-500">Goles Marcados (Prom)</td>
                                <td
                                    class="py-3 px-4 text-right font-bold {{ $metrics['op']['gf_pg'] > $metrics['my']['gf_pg'] ? 'text-green-400' : '' }}">
                                    {{ $metrics['op']['gf_pg'] }}</td>
                            </tr>
                            {{-- Goles en Contra --}}
                            <tr class="hover:bg-white/5 transition">
                                <td
                                    class="py-3 px-4 font-bold {{ $metrics['my']['ga_pg'] < $metrics['op']['ga_pg'] ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $metrics['my']['ga_pg'] }}</td>
                                <td class="py-3 px-4 text-center text-gray-500">Goles Recibidos (Prom)</td>
                                <td
                                    class="py-3 px-4 text-right font-bold {{ $metrics['op']['ga_pg'] < $metrics['my']['ga_pg'] ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $metrics['op']['ga_pg'] }}</td>
                            </tr>
                            {{-- Porterías a Cero --}}
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 px-4 font-bold">{{ $metrics['my']['clean_sheets'] }}
                                    ({{ $metrics['my']['clean_sheets_pct'] }})</td>
                                <td class="py-3 px-4 text-center text-gray-500">Porterías a Cero</td>
                                <td class="py-3 px-4 text-right font-bold">{{ $metrics['op']['clean_sheets'] }}
                                    ({{ $metrics['op']['clean_sheets_pct'] }})</td>
                            </tr>
                            {{-- Partidos sin Marcar --}}
                            <tr class="hover:bg-white/5 transition">
                                <td
                                    class="py-3 px-4 font-bold {{ $metrics['my']['failed_to_score'] < $metrics['op']['failed_to_score'] ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $metrics['my']['failed_to_score'] }} ({{ $metrics['my']['failed_to_score_pct'] }})
                                </td>
                                <td class="py-3 px-4 text-center text-gray-500">Partidos sin Marcar</td>
                                <td
                                    class="py-3 px-4 text-right font-bold {{ $metrics['op']['failed_to_score'] < $metrics['my']['failed_to_score'] ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $metrics['op']['failed_to_score'] }} ({{ $metrics['op']['failed_to_score_pct'] }})
                                </td>
                            </tr>
                            {{-- BTTS --}}
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 px-4 font-bold">{{ $metrics['my']['btts_pct'] }}</td>
                                <td class="py-3 px-4 text-center text-gray-500">Ambos Marcan %</td>
                                <td class="py-3 px-4 text-right font-bold">{{ $metrics['op']['btts_pct'] }}</td>
                            </tr>
                            {{-- Over 2.5 --}}
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 px-4 font-bold">{{ $metrics['my']['over25_pct'] }}</td>
                                <td class="py-3 px-4 text-center text-gray-500">+2.5 Goles %</td>
                                <td class="py-3 px-4 text-right font-bold">{{ $metrics['op']['over25_pct'] }}</td>
                            </tr>
                            {{-- Tarjetas --}}
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 px-4 font-bold text-yellow-400">{{ $metrics['my']['yellows'] }} <span
                                        class="text-red-500 ml-1">{{ $metrics['my']['reds'] }}</span></td>
                                <td class="py-3 px-4 text-center text-gray-500">Tarjetas (A/R)</td>
                                <td class="py-3 px-4 text-right font-bold text-yellow-400">{{ $metrics['op']['yellows'] }}
                                    <span class="text-red-500 ml-1">{{ $metrics['op']['reds'] }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- HOME / AWAY SPLITS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- MI EQUIPO (HOME/AWAY) --}}
                    <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4">
                        <h4 class="text-sm font-bold text-gray-300 mb-4 text-center border-b border-white/5 pb-2">
                            {{ $myTeam->nombre }} - Rendimiento</h4>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase">Local (GF/GC)</p>
                                <p class="text-lg font-bold text-white">{{ $metrics['my']['home_gf'] }} /
                                    {{ $metrics['my']['home_ga'] }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase">Visitante (GF/GC)</p>
                                <p class="text-lg font-bold text-white">{{ $metrics['my']['away_gf'] }} /
                                    {{ $metrics['my']['away_ga'] }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- RIVAL (HOME/AWAY) --}}
                    <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4">
                        <h4 class="text-sm font-bold text-gray-300 mb-4 text-center border-b border-white/5 pb-2">
                            {{ $opponent->nombre }} - Rendimiento</h4>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase">Local (GF/GC)</p>
                                <p class="text-lg font-bold text-white">{{ $metrics['op']['home_gf'] }} /
                                    {{ $metrics['op']['home_ga'] }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase">Visitante (GF/GC)</p>
                                <p class="text-lg font-bold text-white">{{ $metrics['op']['away_gf'] }} /
                                    {{ $metrics['op']['away_ga'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ANÁLISIS DE MOMENTO DE GOL (Gráficos) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- MI EQUIPO --}}
                    <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4">
                        <h4 class="text-sm font-bold text-emerald-400 mb-4 text-center">¿Cuándo marca {{ $myTeam->nombre }}?
                        </h4>
                        @if($goalTiming['my_scored']['total'] > 0)
                            <div class="flex items-end justify-between h-32 gap-1">
                                @foreach($goalTiming['my_scored']['percentages'] as $period => $pct)
                                    <div class="w-full h-full flex flex-col justify-end items-center group relative">
                                        <div class="w-full bg-emerald-500/50 rounded-t hover:bg-emerald-500/80 transition-all relative"
                                            style="height: {{ $pct > 0 ? $pct : 2 }}%">
                                            <span
                                                class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold text-white opacity-0 group-hover:opacity-100 transition">{{ $pct }}%</span>
                                        </div>
                                        <span class="text-[9px] text-gray-500 mt-1">{{ $period }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="h-32 flex items-center justify-center text-gray-500 text-xs italic">Sin datos.</div>
                        @endif
                    </div>

                    {{-- RIVAL --}}
                    <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4">
                        <h4 class="text-sm font-bold text-emerald-400 mb-4 text-center">¿Cuándo marca {{ $opponent->nombre }}?
                        </h4>
                        @if($goalTiming['op_scored']['total'] > 0)
                            <div class="flex items-end justify-between h-32 gap-1">
                                @foreach($goalTiming['op_scored']['percentages'] as $period => $pct)
                                    <div class="w-full h-full flex flex-col justify-end items-center group relative">
                                        <div class="w-full bg-emerald-500/50 rounded-t hover:bg-emerald-500/80 transition-all relative"
                                            style="height: {{ $pct > 0 ? $pct : 2 }}%">
                                            <span
                                                class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold text-white opacity-0 group-hover:opacity-100 transition">{{ $pct }}%</span>
                                        </div>
                                        <span class="text-[9px] text-gray-500 mt-1">{{ $period }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="h-32 flex items-center justify-center text-gray-500 text-xs italic">Sin datos.</div>
                        @endif
                    </div>
                </div>

                {{-- ANÁLISIS DE MOMENTO DE GOL EN CONTRA (NUEVO) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- MI EQUIPO (CONCEDED) --}}
                    <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4">
                        <h4 class="text-sm font-bold text-rose-400 mb-4 text-center">¿Cuándo recibe gol
                            {{ $myTeam->nombre }}?</h4>
                        @if($goalTiming['my_conceded']['total'] > 0)
                            <div class="flex items-end justify-between h-32 gap-1">
                                @foreach($goalTiming['my_conceded']['percentages'] as $period => $pct)
                                    <div class="w-full h-full flex flex-col justify-end items-center group relative">
                                        <div class="w-full bg-rose-500/50 rounded-t hover:bg-rose-500/80 transition-all relative"
                                            style="height: {{ $pct > 0 ? $pct : 2 }}%">
                                            <span
                                                class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold text-white opacity-0 group-hover:opacity-100 transition">{{ $pct }}%</span>
                                        </div>
                                        <span class="text-[9px] text-gray-500 mt-1">{{ $period }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="h-32 flex items-center justify-center text-gray-500 text-xs italic">Sin datos.</div>
                        @endif
                    </div>

                    {{-- RIVAL (CONCEDED) --}}
                    <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4">
                        <h4 class="text-sm font-bold text-rose-400 mb-4 text-center">¿Cuándo recibe gol
                            {{ $opponent->nombre }}?</h4>
                        @if($goalTiming['op_conceded']['total'] > 0)
                            <div class="flex items-end justify-between h-32 gap-1">
                                @foreach($goalTiming['op_conceded']['percentages'] as $period => $pct)
                                    <div class="w-full h-full flex flex-col justify-end items-center group relative">
                                        <div class="w-full bg-rose-500/50 rounded-t hover:bg-rose-500/80 transition-all relative"
                                            style="height: {{ $pct > 0 ? $pct : 2 }}%">
                                            <span
                                                class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold text-white opacity-0 group-hover:opacity-100 transition">{{ $pct }}%</span>
                                        </div>
                                        <span class="text-[9px] text-gray-500 mt-1">{{ $period }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="h-32 flex items-center justify-center text-gray-500 text-xs italic">Sin datos.</div>
                        @endif
                    </div>
                </div>

                {{-- HISTORIAL RECIENTE DETALLADO (NUEVO) --}}
                <div class="bg-card-bg/80 border border-white/10 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-gray-400"></i>
                        Historial Reciente (Últimos 5)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- MI EQUIPO --}}
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">{{ $myTeam->nombre }}</h4>
                            <div class="space-y-2">
                                @foreach($recentHistory['my'] as $match)
                                    <div class="flex justify-between items-center text-sm bg-white/5 p-2 rounded">
                                        <span class="text-gray-400 text-xs">{{ $match['date'] }}</span>
                                        <span class="text-white truncate flex-1 mx-2">vs {{ $match['opponent'] }}</span>
                                        <span
                                            class="font-bold {{ $match['result'] == 'G' ? 'text-green-400' : ($match['result'] == 'P' ? 'text-red-400' : 'text-yellow-400') }}">
                                            {{ $match['score'] }} ({{ $match['result'] }})
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        {{-- RIVAL --}}
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">{{ $opponent->nombre }}</h4>
                            <div class="space-y-2">
                                @foreach($recentHistory['op'] as $match)
                                    <div class="flex justify-between items-center text-sm bg-white/5 p-2 rounded">
                                        <span class="text-gray-400 text-xs">{{ $match['date'] }}</span>
                                        <span class="text-white truncate flex-1 mx-2">vs {{ $match['opponent'] }}</span>
                                        <span
                                            class="font-bold {{ $match['result'] == 'G' ? 'text-green-400' : ($match['result'] == 'P' ? 'text-red-400' : 'text-yellow-400') }}">
                                            {{ $match['score'] }} ({{ $match['result'] }})
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- COLUMNA DERECHA: COACH'S CORNER & ALINEACIÓN & SQUAD DNA --}}
            <div class="lg:col-span-4 space-y-6">

                {{-- ESTRATEGIA SUGERIDA --}}
                <div class="bg-[#1a1f2e] border border-l-4 border-l-cyan-500 border-white/10 rounded-r-xl p-6 shadow-lg">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-cyan-400"></i>
                        Informe de Scouting IA
                    </h3>
                    <div class="mb-6">
                        <p class="text-[10px] text-cyan-500 uppercase tracking-widest font-bold mb-1">Enfoque Táctico</p>
                        <p class="text-2xl font-black text-white leading-tight">{{ $strategy['focus'] }}</p>
                    </div>
                    <div class="space-y-4">
                        @foreach($strategy['tips'] as $index => $tip)
                            <div class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 rounded-full bg-cyan-900/50 text-cyan-400 flex items-center justify-center text-xs font-bold border border-cyan-500/30">{{ $index + 1 }}</span>
                                <p class="text-sm text-gray-300 leading-snug">{{ $tip }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- SQUAD DNA (NUEVO) --}}
                <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4">
                    <h3 class="text-sm font-bold text-white mb-3 uppercase tracking-widest border-b border-white/5 pb-2">
                        Squad DNA: {{ $opponent->nombre }}</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center bg-white/5 p-2 rounded">
                            <p class="text-2xl font-black text-white">{{ $squadStats['op']['size'] }}</p>
                            <p class="text-[10px] text-gray-500 uppercase">Jugadores</p>
                        </div>
                        <div class="text-center bg-white/5 p-2 rounded">
                            <p class="text-2xl font-black text-green-400">{{ $squadStats['op']['total_goals'] }}</p>
                            <p class="text-[10px] text-gray-500 uppercase">Goles Totales</p>
                        </div>
                    </div>

                    {{-- CREATIVE HUB (Top Assisters) --}}
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Creative Hub (Asistencias)</h4>
                    <div class="space-y-2 mb-4">
                        @foreach($squadStats['op']['top_assisters'] as $player)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-white">{{ $player->nombre }}</span>
                                <span class="text-cyan-400 font-bold">{{ $player->asistencias }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- THE WALL (Portero) --}}
                    @if($squadStats['op']['goalkeeper'])
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">The Wall (Portero)</h4>
                        <div class="flex items-center gap-3 bg-white/5 p-2 rounded">
                            <img src="{{ $squadStats['op']['goalkeeper']->foto_url ?? 'https://placehold.co/40x40' }}"
                                class="w-10 h-10 rounded-full border border-gray-600">
                            <div>
                                <p class="text-sm font-bold text-white leading-none">
                                    {{ $squadStats['op']['goalkeeper']->nombre }}</p>
                                <p class="text-xs text-gray-500">{{ $squadStats['op']['goalkeeper']->paradas }} Paradas</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ALINEACIÓN SUGERIDA --}}
                <div class="bg-card-bg/80 backdrop-blur-md border border-white/10 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-people-group text-green-400"></i>
                        11 Ideal Sugerido
                    </h3>
                    <div class="space-y-2">
                        @forelse($suggestedLineup as $player)
                            <div
                                class="flex items-center justify-between text-sm border-b border-white/5 pb-2 last:border-0 hover:bg-white/5 px-2 rounded transition">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-[10px] font-bold w-8 uppercase bg-gray-800 text-gray-400 px-1 py-0.5 rounded text-center"
                                        title="{{ $player->posicion_general }}">
                                        {{ substr($player->posicion_general, 0, 3) }}
                                    </span>
                                    <span class="text-white truncate font-medium">{{ $player->nombre }}</span>
                                </div>
                                <span class="text-xs font-bold text-green-400">{{ $player->goles + $player->asistencias }}
                                    G/A</span>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-center text-xs">Datos insuficientes.</p>
                        @endforelse
                    </div>
                </div>

                {{-- DUELO DE ESTRELLAS --}}
            <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4">
                <h4 class="text-xs font-bold text-gray-400 uppercase mb-3 text-center">Jugadores a Seguir (Goleadores)</h4>
                <div class="flex justify-between items-center">
                    @php
                        $myTopScorer = $squadStats['my']['top_scorers']->first();
                        $opTopScorer = $squadStats['op']['top_scorers']->first();
                    @endphp

                    @if($myTopScorer)
                        <div class="text-center">
                            <img src="{{ $myTopScorer->foto_url ?? 'https://placehold.co/50x50' }}" class="w-12 h-12 rounded-full border border-primary mx-auto mb-1">
                            <p class="text-xs text-white font-bold">{{ explode(' ', $myTopScorer->nombre)[0] }}</p>
                            <p class="text-[10px] text-primary">{{ $myTopScorer->goles }} Goles</p>
                        </div>
                    @endif
                    <span class="text-gray-600 font-black text-xl">VS</span>
                    @if($opTopScorer)
                        <div class="text-center">
                            <img src="{{ $opTopScorer->foto_url ?? 'https://placehold.co/50x50' }}" class="w-12 h-12 rounded-full border border-red-500 mx-auto mb-1">
                            <p class="text-xs text-white font-bold">{{ explode(' ', $opTopScorer->nombre)[0] }}</p>
                            <p class="text-[10px] text-red-500">{{ $opTopScorer->goles }} Goles</p>
                        </div>
                    @endif
                </div>
            </div>

            </div>
        </div>
    </div>
@endsection