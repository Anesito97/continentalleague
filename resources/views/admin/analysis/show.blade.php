@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">

        {{-- 1. HEADER DEL ENFRENTAMIENTO --}}
        <div class="bg-card-bg border border-white/10 rounded-xl p-4 sm:p-6 mb-6 relative overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/10 via-transparent to-red-500/10 opacity-40"></div>

            <div class="flex flex-col sm:flex-row items-center justify-between relative z-10 gap-4 sm:gap-0">
                {{-- MI EQUIPO --}}
                <div class="flex items-center gap-4 w-full sm:w-1/3 justify-center sm:justify-start">
                    <img src="{{ $myTeam->escudo_url ?? 'https://placehold.co/100x100' }}"
                        class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-lg">
                    <div class="text-center sm:text-left">
                        <h2 class="text-2xl sm:text-3xl font-black text-white leading-none">{{ $myTeam->nombre }}</h2>
                        <span class="text-primary font-bold text-xs uppercase tracking-widest">Tu Equipo</span>
                    </div>
                </div>

                {{-- PROBABILIDAD DE VICTORIA (GAUGE) --}}
                <div class="flex flex-col items-center justify-center w-full sm:w-1/3">
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
                <div
                    class="flex flex-row-reverse sm:flex-row items-center gap-4 w-full sm:w-1/3 justify-center sm:justify-end text-right">
                    <div class="text-center sm:text-right">
                        <h2 class="text-2xl sm:text-3xl font-black text-white leading-none">{{ $opponent->nombre }}</h2>
                        <span class="text-red-500 font-bold text-xs uppercase tracking-widest">El Rival</span>
                    </div>
                    <img src="{{ $opponent->escudo_url ?? 'https://placehold.co/100x100' }}"
                        class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-lg">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- COLUMNA IZQUIERDA: DATOS DUROS --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- TALE OF THE TAPE (VISUAL METRICS) --}}
                <div class="bg-card-bg/90 backdrop-blur-md border border-white/10 rounded-xl overflow-hidden">
                    <div class="bg-black/20 p-4 border-b border-white/5 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-scale-balanced text-yellow-500"></i>
                            Comparativa Directa
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-8">
                        @php
                            $groups = [
                                'GENERAL' => [
                                    ['label' => 'Puntos x Partido', 'key' => 'ppg', 'format' => 'number'],
                                ],
                                'ATAQUE' => [
                                    ['label' => 'Goles a Favor (Prom)', 'key' => 'gf_pg', 'format' => 'number'],
                                    ['label' => 'Partidos sin Marcar', 'key' => 'failed_to_score_pct', 'format' => 'percent', 'inverse' => true], // Inverse: Lower is better
                                    ['label' => '+2.5 Goles', 'key' => 'over25_pct', 'format' => 'percent'],
                                ],
                                'DEFENSA' => [
                                    ['label' => 'Goles en Contra (Prom)', 'key' => 'ga_pg', 'format' => 'number', 'inverse' => true],
                                    ['label' => 'Porterías a Cero', 'key' => 'clean_sheets_pct', 'format' => 'percent'],
                                    ['label' => 'Ambos Marcan', 'key' => 'btts_pct', 'format' => 'percent'],
                                ],
                                'DISCIPLINA' => [
                                    ['label' => 'Tarjetas Amarillas', 'key' => 'yellows', 'format' => 'number', 'inverse' => true],
                                    ['label' => 'Tarjetas Rojas', 'key' => 'reds', 'format' => 'number', 'inverse' => true],
                                ]
                            ];
                        @endphp

                        @foreach($groups as $groupName => $items)
                            <div>
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-white/5 pb-1">{{ $groupName }}</h4>
                                <div class="space-y-4">
                                    @foreach($items as $item)
                                        @php
                                            $myValRaw = $metrics['my'][$item['key']];
                                            $opValRaw = $metrics['op'][$item['key']];
                                            
                                            // Clean values for calculation
                                            $myVal = (float) str_replace('%', '', $myValRaw);
                                            $opVal = (float) str_replace('%', '', $opValRaw);
                                            
                                            $max = max($myVal, $opVal);
                                            $max = $max == 0 ? 1 : $max; // Avoid division by zero
                                            
                                            $myWidth = ($myVal / $max) * 100;
                                            $opWidth = ($opVal / $max) * 100;

                                            // Determine winner color
                                            $inverse = $item['inverse'] ?? false;
                                            $myColor = 'bg-gray-600';
                                            $opColor = 'bg-gray-600';
                                            $myText = 'text-gray-400';
                                            $opText = 'text-gray-400';

                                            if ($myVal != $opVal) {
                                                if (($myVal > $opVal && !$inverse) || ($myVal < $opVal && $inverse)) {
                                                    $myColor = 'bg-primary';
                                                    $myText = 'text-primary font-bold';
                                                } else {
                                                    $opColor = 'bg-red-500';
                                                    $opText = 'text-red-500 font-bold';
                                                }
                                            }
                                        @endphp

                                        <div class="relative grid grid-cols-7 items-center gap-2 group">
                                            {{-- My Value --}}
                                            <div class="col-span-1 text-right text-sm {{ $myText }}">{{ $myValRaw }}</div>
                                            
                                            {{-- My Bar (Right aligned) --}}
                                            <div class="col-span-2 flex justify-end">
                                                <div class="h-2 rounded-l-full {{ $myColor }} transition-all duration-1000" style="width: {{ $myWidth }}%"></div>
                                            </div>

                                            {{-- Label (Center) --}}
                                            <div class="col-span-1 text-center text-[10px] text-gray-500 uppercase font-bold leading-tight">{{ $item['label'] }}</div>

                                            {{-- Op Bar (Left aligned) --}}
                                            <div class="col-span-2 flex justify-start">
                                                <div class="h-2 rounded-r-full {{ $opColor }} transition-all duration-1000" style="width: {{ $opWidth }}%"></div>
                                            </div>

                                            {{-- Op Value --}}
                                            <div class="col-span-1 text-left text-sm {{ $opText }}">{{ $opValRaw }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
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
                        <h4 class="text-sm font-bold text-emerald-400 mb-4 text-center">¿Cuándo marca
                            {{ $opponent->nombre }}?</h4>
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

                {{-- ANÁLISIS DE MOMENTO DE GOL EN CONTRA --}}
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

                {{-- HISTORIAL RECIENTE DETALLADO --}}
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

                {{-- ESTRATEGIA SUGERIDA (MATCH PLAN) --}}
                <div class="bg-[#1a1f2e] border border-white/10 rounded-xl p-6 shadow-lg">
                    <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-cyan-400"></i>
                        Plan de Partido (IA)
                    </h3>
                    
                    <div class="space-y-6">
                        {{-- ATAQUE --}}
                        <div>
                            <h4 class="text-xs font-bold text-green-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <i class="fa-solid fa-bullseye"></i> Fase Ofensiva
                            </h4>
                            <ul class="space-y-2">
                                @foreach($strategy['attack'] as $tip)
                                    <li class="flex gap-2 text-sm text-gray-300 bg-green-500/5 p-2 rounded border-l-2 border-green-500">
                                        <i class="fa-solid fa-check text-green-400 mt-1 text-xs"></i>
                                        <span>{{ $tip }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- DEFENSA --}}
                        <div>
                            <h4 class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved"></i> Fase Defensiva
                            </h4>
                            <ul class="space-y-2">
                                @foreach($strategy['defense'] as $tip)
                                    <li class="flex gap-2 text-sm text-gray-300 bg-blue-500/5 p-2 rounded border-l-2 border-blue-500">
                                        <i class="fa-solid fa-shield text-blue-400 mt-1 text-xs"></i>
                                        <span>{{ $tip }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- ALERTAS --}}
                        @if(!empty($strategy['alerts']) && $strategy['alerts'][0] != "Partido estándar. Sin anomalías estadísticas graves.")
                        <div>
                            <h4 class="text-xs font-bold text-red-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <i class="fa-solid fa-triangle-exclamation"></i> Alertas Clave
                            </h4>
                            <ul class="space-y-2">
                                @foreach($strategy['alerts'] as $tip)
                                    <li class="flex gap-2 text-sm text-white bg-red-500/10 p-2 rounded border-l-2 border-red-500 animate-pulse">
                                        <i class="fa-solid fa-circle-exclamation text-red-500 mt-1 text-xs"></i>
                                        <span>{{ $tip }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- VISUAL PITCH CAROUSEL (VARIANTES) --}}
            <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4 relative" x-data="{ activeSlide: 0, variants: {{ count($variants) }} }">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-chess-board text-green-400"></i>
                        Pizarra Táctica
                    </h3>
                    {{-- Carousel Controls --}}
                    <div class="flex gap-2">
                        <button @click="activeSlide = activeSlide === 0 ? variants - 1 : activeSlide - 1" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button @click="activeSlide = activeSlide === variants - 1 ? 0 : activeSlide + 1" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                @foreach($variants as $index => $variant)
                    <div x-show="activeSlide === {{ $index }}" class="transition-opacity duration-300" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                        
                        {{-- Header del Variante --}}
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-bold text-cyan-400 uppercase tracking-widest">{{ $variant['type'] }}</span>
                            <span class="text-xs font-bold text-white bg-white/10 px-2 py-1 rounded">{{ $variant['formation'] }}</span>
                        </div>

                        {{-- Razonamiento --}}
                        <div class="bg-blue-500/10 border border-blue-500/20 p-3 rounded mb-4 min-h-[60px] flex items-center">
                            <p class="text-xs text-blue-300 italic">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                {{ $variant['reasoning'] }}
                            </p>
                        </div>

                        {{-- Pitch --}}
                        <div class="relative w-full aspect-[2/3] bg-green-800 rounded-lg border-4 border-white shadow-2xl overflow-hidden select-none mx-auto max-w-[300px]">
                            {{-- Pitch Lines --}}
                            <div class="absolute inset-0 opacity-20" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 20px, #000 20px, #000 40px);"></div>
                            <div class="absolute inset-4 border-2 border-white/50"></div>
                            <div class="absolute top-1/2 left-4 right-4 h-0.5 bg-white/50 -translate-y-1/2"></div>
                            <div class="absolute top-1/2 left-1/2 w-24 h-24 border-2 border-white/50 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                            <div class="absolute top-4 left-1/2 w-48 h-24 border-2 border-t-0 border-white/50 -translate-x-1/2 bg-white/5"></div>
                            <div class="absolute bottom-4 left-1/2 w-48 h-24 border-2 border-b-0 border-white/50 -translate-x-1/2 bg-white/5"></div>

                            {{-- Players Positioning --}}
                            @php
                                $formations = [
                                    '4-4-2' => [
                                        ['top' => '88%', 'left' => '50%'], // GK
                                        ['top' => '68%', 'left' => '15%'], ['top' => '72%', 'left' => '38%'], ['top' => '72%', 'left' => '62%'], ['top' => '68%', 'left' => '85%'], // DEF
                                        ['top' => '45%', 'left' => '15%'], ['top' => '50%', 'left' => '38%'], ['top' => '50%', 'left' => '62%'], ['top' => '45%', 'left' => '85%'], // MID
                                        ['top' => '18%', 'left' => '35%'], ['top' => '18%', 'left' => '65%'] // FWD
                                    ],
                                    '4-3-3' => [
                                        ['top' => '88%', 'left' => '50%'], // GK
                                        ['top' => '68%', 'left' => '15%'], ['top' => '72%', 'left' => '38%'], ['top' => '72%', 'left' => '62%'], ['top' => '68%', 'left' => '85%'], // DEF
                                        ['top' => '48%', 'left' => '30%'], ['top' => '52%', 'left' => '50%'], ['top' => '48%', 'left' => '70%'], // MID
                                        ['top' => '20%', 'left' => '20%'], ['top' => '15%', 'left' => '50%'], ['top' => '20%', 'left' => '80%'] // FWD
                                    ],
                                    '5-4-1' => [
                                        ['top' => '88%', 'left' => '50%'], // GK
                                        ['top' => '60%', 'left' => '10%'], ['top' => '70%', 'left' => '30%'], ['top' => '72%', 'left' => '50%'], ['top' => '70%', 'left' => '70%'], ['top' => '60%', 'left' => '90%'], // DEF
                                        ['top' => '45%', 'left' => '25%'], ['top' => '48%', 'left' => '42%'], ['top' => '48%', 'left' => '58%'], ['top' => '45%', 'left' => '75%'], // MID
                                        ['top' => '15%', 'left' => '50%'] // ST
                                    ],
                                    '3-4-3' => [
                                        ['top' => '88%', 'left' => '50%'], // GK
                                        ['top' => '70%', 'left' => '25%'], ['top' => '72%', 'left' => '50%'], ['top' => '70%', 'left' => '75%'], // DEF
                                        ['top' => '45%', 'left' => '15%'], ['top' => '50%', 'left' => '38%'], ['top' => '50%', 'left' => '62%'], ['top' => '45%', 'left' => '85%'], // MID
                                        ['top' => '20%', 'left' => '20%'], ['top' => '15%', 'left' => '50%'], ['top' => '20%', 'left' => '80%'] // FWD
                                    ],
                                    '5-3-2' => [
                                        ['top' => '88%', 'left' => '50%'], // GK
                                        ['top' => '60%', 'left' => '10%'], ['top' => '70%', 'left' => '30%'], ['top' => '72%', 'left' => '50%'], ['top' => '70%', 'left' => '70%'], ['top' => '60%', 'left' => '90%'], // DEF
                                        ['top' => '48%', 'left' => '30%'], ['top' => '52%', 'left' => '50%'], ['top' => '48%', 'left' => '70%'], // MID
                                        ['top' => '18%', 'left' => '35%'], ['top' => '18%', 'left' => '65%'] // FWD
                                    ]
                                ];
                                
                                $currentPositions = $formations[$variant['formation']] ?? $formations['4-4-2'];
                                $pIndex = 0;
                            @endphp

                            @foreach($variant['lineup'] as $player)
                                @if($pIndex < count($currentPositions))
                                    @php $pos = $currentPositions[$pIndex]; $pIndex++; @endphp
                                    <div class="absolute transform -translate-x-1/2 -translate-y-1/2 flex flex-col items-center" style="top: {{ $pos['top'] }}; left: {{ $pos['left'] }};">
                                        <div class="w-8 h-8 rounded-full border border-white overflow-hidden bg-gray-800 shadow-md">
                                            <img src="{{ $player->foto_url ?? 'https://placehold.co/40x40' }}" class="w-full h-full object-cover">
                                        </div>
                                        <span class="text-[8px] font-bold text-white bg-black/50 px-1 rounded mt-0.5 whitespace-nowrap">{{ explode(' ', $player->nombre)[0] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

                {{-- SUBSTITUCIONES (NUEVO) --}}
                @if(count($substitutions) > 0)
                    <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4">
                        <h3 class="text-sm font-bold text-white mb-3 uppercase tracking-widest border-b border-white/5 pb-2">
                            <i class="fa-solid fa-arrow-right-arrow-left text-yellow-400 mr-1"></i> Cambios Sugeridos
                        </h3>
                        <div class="space-y-3">
                            @foreach($substitutions as $sub)
                                <div class="bg-white/5 p-3 rounded text-sm">
                                    <p class="text-xs text-yellow-400 font-bold mb-1">{{ $sub['scenario'] }}</p>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-green-400 font-bold">IN: {{ $sub['in']->nombre }}</span>
                                        <span class="text-gray-500">x</span>
                                        <span class="text-red-400 text-xs">{{ $sub['out_position'] }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400 italic">"{{ $sub['reason'] }}"</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- MARCA PERSONAL (NUEVO) --}}
                @if($manMarking)
                    <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4">
                        <h3 class="text-sm font-bold text-white mb-3 uppercase tracking-widest border-b border-white/5 pb-2">
                            <i class="fa-solid fa-crosshairs text-red-500 mr-1"></i> Marca Personal
                        </h3>
                        <div class="bg-red-500/10 border border-red-500/20 p-3 rounded text-sm">
                            <div class="flex justify-between items-center mb-2">
                                <div class="text-center">
                                    <img src="{{ $manMarking['marker']->foto_url ?? 'https://placehold.co/40x40' }}"
                                        class="w-8 h-8 rounded-full mx-auto border border-white">
                                    <p class="text-[10px] text-white mt-1">{{ $manMarking['marker']->nombre }}</p>
                                </div>
                                <i class="fa-solid fa-lock text-red-400"></i>
                                <div class="text-center">
                                    <img src="{{ $manMarking['target']->foto_url ?? 'https://placehold.co/40x40' }}"
                                        class="w-8 h-8 rounded-full mx-auto border border-red-500">
                                    <p class="text-[10px] text-white mt-1">{{ $manMarking['target']->nombre }}</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-300 text-center">{{ $manMarking['instruction'] }}</p>
                        </div>
                    </div>
                @endif

                {{-- SQUAD DNA --}}
                <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4">
                    <h3 class="text-sm font-bold text-gray-400 mb-4 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-dna text-blue-400"></i>
                        Squad DNA: {{ $opponent->nombre }}
                    </h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-white/5 rounded-lg p-3 text-center">
                            <span class="block text-2xl font-bold text-white">{{ $squadStats['op']['size'] }}</span>
                            <span class="text-xs text-gray-400 uppercase">Jugadores</span>
                        </div>
                        <div class="bg-white/5 rounded-lg p-3 text-center">
                            <span class="block text-2xl font-bold text-green-400">{{ $squadStats['op']['total_goals'] }}</span>
                            <span class="text-xs text-gray-400 uppercase">Goles Totales</span>
                        </div>
                    </div>
                    
                    {{-- DNA Insight --}}
                    <div class="space-y-2 mb-4">
                        <div class="bg-blue-500/10 border border-blue-500/20 p-2 rounded flex items-start gap-2">
                            <i class="fa-solid fa-magnifying-glass text-blue-400 mt-1 text-xs"></i>
                            <p class="text-xs text-blue-200">{{ $squadStats['op']['dna_insight']['reason'] }}</p>
                        </div>
                        <div class="bg-red-500/10 border border-red-500/20 p-2 rounded flex items-start gap-2">
                            <i class="fa-solid fa-shield-halved text-red-400 mt-1 text-xs"></i>
                            <p class="text-xs text-red-200">{{ $squadStats['op']['dna_insight']['strategy'] }}</p>
                        </div>
                    </div>

                    <h4 class="text-xs font-bold text-gray-500 mb-2 uppercase">Creative Hub (Asistencias)</h4>
                    <div class="space-y-2 mb-4">
                        @foreach($squadStats['op']['top_assisters'] as $player)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-white">{{ $player->nombre }}</span>
                                <span class="font-bold text-cyan-400">{{ $player->asistencias }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Creative Insight --}}
                    <div class="space-y-2">
                        <div class="bg-cyan-500/10 border border-cyan-500/20 p-2 rounded flex items-start gap-2">
                            <i class="fa-solid fa-brain text-cyan-400 mt-1 text-xs"></i>
                            <p class="text-xs text-cyan-200">{{ $squadStats['op']['creative_insight']['reason'] }}</p>
                        </div>
                        <div class="bg-orange-500/10 border border-orange-500/20 p-2 rounded flex items-start gap-2">
                            <i class="fa-solid fa-hand-paper text-orange-400 mt-1 text-xs"></i>
                            <p class="text-xs text-orange-200">{{ $squadStats['op']['creative_insight']['strategy'] }}</p>
                        </div>
                    </div>

                {{-- THE WALL --}}
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

                {{-- DUEL OF THE DAY --}}
                @if(isset($keyMatchup))
                <div class="bg-card-bg/80 border border-white/10 rounded-xl p-4 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-red-500"></div>
                    <h3 class="text-sm font-bold text-gray-400 mb-6 uppercase tracking-wider text-center">
                        <i class="fa-solid fa-fire text-orange-500 mr-2"></i>Duel of the Day
                    </h3>
                    
                    <div class="flex items-center justify-between relative">
                        {{-- My Player --}}
                        <div class="flex flex-col items-center w-1/3 relative z-10">
                            <div class="w-16 h-16 rounded-full p-1 bg-gradient-to-br from-primary to-emerald-600 shadow-lg shadow-primary/30 mb-2">
                                <img src="{{ $keyMatchup['my']->foto_url ?? 'https://placehold.co/64x64' }}" class="w-full h-full rounded-full object-cover border-2 border-gray-900">
                            </div>
                            <span class="text-white font-bold text-sm text-center leading-tight">{{ $keyMatchup['my']->nombre }}</span>
                            <span class="text-[10px] text-primary font-bold uppercase">{{ $myTeam->nombre }}</span>
                        </div>

                        {{-- VS Badge --}}
                        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-0 opacity-20 text-6xl font-black text-white italic">VS</div>

                        {{-- Stats Table --}}
                        <div class="w-1/3 z-10">
                            <div class="space-y-2">
                                {{-- Goals --}}
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold {{ $keyMatchup['comparison']['goals']['my'] >= $keyMatchup['comparison']['goals']['op'] ? 'text-primary' : 'text-gray-500' }}">
                                        {{ $keyMatchup['comparison']['goals']['my'] }}
                                    </span>
                                    <span class="text-gray-400 uppercase text-[10px]">Goles</span>
                                    <span class="font-bold {{ $keyMatchup['comparison']['goals']['op'] >= $keyMatchup['comparison']['goals']['my'] ? 'text-red-500' : 'text-gray-500' }}">
                                        {{ $keyMatchup['comparison']['goals']['op'] }}
                                    </span>
                                </div>
                                {{-- Assists --}}
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold {{ $keyMatchup['comparison']['assists']['my'] >= $keyMatchup['comparison']['assists']['op'] ? 'text-primary' : 'text-gray-500' }}">
                                        {{ $keyMatchup['comparison']['assists']['my'] }}
                                    </span>
                                    <span class="text-gray-400 uppercase text-[10px]">Asist</span>
                                    <span class="font-bold {{ $keyMatchup['comparison']['assists']['op'] >= $keyMatchup['comparison']['assists']['my'] ? 'text-red-500' : 'text-gray-500' }}">
                                        {{ $keyMatchup['comparison']['assists']['op'] }}
                                    </span>
                                </div>
                                {{-- G/Match --}}
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold {{ $keyMatchup['comparison']['gpg']['my'] >= $keyMatchup['comparison']['gpg']['op'] ? 'text-primary' : 'text-gray-500' }}">
                                        {{ $keyMatchup['comparison']['gpg']['my'] }}
                                    </span>
                                    <span class="text-gray-400 uppercase text-[10px]">G/P</span>
                                    <span class="font-bold {{ $keyMatchup['comparison']['gpg']['op'] >= $keyMatchup['comparison']['gpg']['my'] ? 'text-red-500' : 'text-gray-500' }}">
                                        {{ $keyMatchup['comparison']['gpg']['op'] }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Opponent Player --}}
                        <div class="flex flex-col items-center w-1/3 relative z-10">
                            <div class="w-16 h-16 rounded-full p-1 bg-gradient-to-br from-red-500 to-orange-600 shadow-lg shadow-red-500/30 mb-2">
                                <img src="{{ $keyMatchup['op']->foto_url ?? 'https://placehold.co/64x64' }}" class="w-full h-full rounded-full object-cover border-2 border-gray-900">
                            </div>
                            <span class="text-white font-bold text-sm text-center leading-tight">{{ $keyMatchup['op']->nombre }}</span>
                            <span class="text-[10px] text-red-400 font-bold uppercase">{{ $opponent->nombre }}</span>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
@endsection
```