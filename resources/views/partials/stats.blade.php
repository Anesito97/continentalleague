{{-- Determine the active tab from the URL, default to 'scorers' --}}
@php
    $activeStat = request()->query('stat', 'scorers');
    
    // ⬇️ CORRECCIÓN: Aplicar límites directamente a las colecciones ordenadas ⬇️
    if ($activeStat === 'scorers') {
        $fullRanking = $topScorers;
        $statName = 'Goles';
        $statColor = 'text-red-400';
    } elseif ($activeStat === 'assists') {
        $fullRanking = $topAssists;
        $statName = 'Asistencias';
        $statColor = 'text-yellow-400';
    } elseif ($activeStat === 'keepers') {
        $fullRanking = $topKeepers;
        $statName = 'Paradas';
        $statColor = 'text-blue-400';
    } else {
        $fullRanking = collect([]);
    }
    
    // Aplicamos los límites de la vista al ranking completo
    $currentTop3 = $fullRanking->take(3);
    $currentList = $fullRanking->skip(3); 
@endphp


<h2 class="text-3xl font-bold text-white mb-4">Líderes de la Liga</h2>

{{-- ---------------------------------------------------- --}}
{{-- 1. TABS: Toggle between Scorers, Assists, and Keepers --}}
{{-- ---------------------------------------------------- --}}
<div class="flex space-x-4 mb-8 border-b border-gray-700">
    <a href="{{ route('home', ['view' => 'stats', 'stat' => 'scorers']) }}"
       class="py-2 px-4 font-semibold transition duration-200 {{ $activeStat === 'scorers' ? 'text-red-400 border-b-2 border-red-400' : 'text-gray-400 hover:text-white' }}">
        Top Goleadores
    </a>
    <a href="{{ route('home', ['view' => 'stats', 'stat' => 'assists']) }}"
       class="py-2 px-4 font-semibold transition duration-200 {{ $activeStat === 'assists' ? 'text-yellow-400 border-b-2 border-yellow-400' : 'text-gray-400 hover:text-white' }}">
        Top Asistentes
    </a>
    <a href="{{ route('home', ['view' => 'stats', 'stat' => 'keepers']) }}"
       class="py-2 px-4 font-semibold transition duration-200 {{ $activeStat === 'keepers' ? 'text-blue-400 border-b-2 border-blue-400' : 'text-gray-400 hover:text-white' }}">
        Top Porteros
    </a>
</div>


{{-- ---------------------------------------------------- --}}
{{-- 2. TOP 3 CARDS (The visual highlight) --}}
{{-- ---------------------------------------------------- --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    @forelse($currentTop3 as $i => $player)
        @php
            $rank = $loop->iteration;
            
            $statValue = ($activeStat === 'scorers') ? $player->goles : (($activeStat === 'assists') ? $player->asistencias : $player->paradas);
            $cardColor = $rank === 1 ? 'border-primary' : ($rank === 2 ? 'border-secondary' : 'border-gray-500');
            $posColor = $rank === 1 ? 'text-primary' : ($rank === 2 ? 'text-secondary' : 'text-gray-400');
            $backgroundColor = $rank === 1 ? 'bg-primary/20' : ($rank === 2 ? 'bg-secondary/20' : 'bg-gray-700/50');
        @endphp
        
        <div class="card p-3 sm:p-4 flex justify-between items-center border-b-4 {{ $cardColor }} shadow-xl {{ $backgroundColor }}">
            
            {{-- SECCIÓN IZQUIERDA: Rank, Info y FOTO --}}
            <div class="flex items-center space-x-3">
                
                {{-- Rank Destacado --}}
                <div class="text-3xl font-extrabold px-3 py-1 rounded-lg {{ $posColor }} flex-shrink-0">
                    {{ $rank }}
                </div>
                
                {{-- ⬇️ IMAGEN Y TEXTO ⬇️ --}}
                <div class="flex items-center space-x-2">
                    <img src="{{ $player->foto_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=JUG' }}"
                        onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=JUG'"
                        class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                    
                    <div class="flex flex-col">
                        <p class="text-lg font-bold text-white leading-tight">{{ $player->nombre }}</p>
                        <p class="text-xs text-gray-400">{{ $player->equipo->nombre ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN DERECHA: Stat Value --}}
            <div class="text-right flex items-center space-x-2">
                {{-- Stat Count --}}
                <div class="p-2 sm:p-3 rounded-lg bg-black/30 flex-shrink-0">
                    <span class="text-3xl font-extrabold {{ $statColor }} block leading-none">{{ $statValue }}</span>
                </div>
            </div>
        </div>
    @empty
        <p class="text-white/70 md:col-span-3">No hay suficientes datos para mostrar el Top 3.</p>
    @endforelse
</div>


{{-- ---------------------------------------------------- --}}
{{-- 3. DETAILED LIST (Rank 4 en adelante) --}}
{{-- ---------------------------------------------------- --}}
@if($currentList->isNotEmpty())
<div class="bg-card-bg rounded-lg overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/5 text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 bg-gray-800">
                    <th class="py-3 px-4 w-1/12">Rank</th>
                    <th class="py-3 px-4 w-5/12">Jugador</th>
                    <th class="py-3 px-4 text-center hidden sm:table-cell">PJ</th> 
                    <th class="py-3 px-4 text-right">{{ $statName }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($currentList as $player)
                    @php
                        // FIX: Calcular el rank basado en la posición en la colección, sumando el 3 que ya saltamos
                        $rank = $loop->index + 4; 
                        $statValue = ($activeStat === 'scorers') ? $player->goles : (($activeStat === 'assists') ? $player->asistencias : $player->paradas);
                        $matchesPlayed = $player->equipo->partidos_jugados ?? 0;
                    @endphp
                    <tr class="hover:bg-white/5 transition duration-150">
                        <td class="py-3 px-4 text-center font-bold text-white/70">{{ $rank }}</td>
                        <td class="py-3 px-4 font-medium flex items-center gap-3">
                            <img src="{{ $player->foto_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=JUG' }}"
                                onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=JUG'"
                                class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                            <div class="flex flex-col">
                                <span class="text-white">{{ $player->nombre }}</span>
                                <span class="text-xs text-gray-500">{{ $player->equipo->nombre ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center hidden sm:table-cell text-white/70">{{ $matchesPlayed }}</td> 
                        <td class="py-3 px-4 text-right font-bold {{ $statColor }}">{{ $statValue }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif