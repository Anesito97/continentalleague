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


{{-- MEJORA: Título con gradiente --}}
<h2 class="text-3xl font-bold mb-4 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
    Líderes de la Liga
</h2>

{{-- ---------------------------------------------------- --}}
{{-- 1. TABS: Toggle between Scorers, Assists, and Keepers --}}
{{-- ---------------------------------------------------- --}}
{{-- MEJORA: Tabs estilo "Píldora" (Pills) --}}
<div class="flex space-x-2 sm:space-x-4 mb-8">
    <a href="{{ route('home', ['view' => 'stats', 'stat' => 'scorers']) }}"
        class="py-2 px-4 font-semibold transition-all duration-300 rounded-md {{ $activeStat === 'scorers' ? 'bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
        Goleadores
    </a>
    <a href="{{ route('home', ['view' => 'stats', 'stat' => 'assists']) }}"
        class="py-2 px-4 font-semibold transition-all duration-300 rounded-md {{ $activeStat === 'assists' ? 'bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
        Asistentes
    </a>
    <a href="{{ route('home', ['view' => 'stats', 'stat' => 'keepers']) }}"
        class="py-2 px-4 font-semibold transition-all duration-300 rounded-md {{ $activeStat === 'keepers' ? 'bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
        Porteros
    </a>
</div>


{{-- ---------------------------------------------------- --}}
{{-- 2. TOP 3 CARDS (The visual highlight) --}}
{{-- ---------------------------------------------------- --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    @forelse($currentTop3 as $i => $player)
        @php
            $rank = $loop->iteration;

            $statValue =
                $activeStat === 'scorers'
                    ? $player->goles
                    : ($activeStat === 'assists'
                        ? $player->asistencias
                        : $player->paradas);
            
            // Lógica de Estilos del Podio
            $cardColor = 'border-gray-500';
            $posColor = 'text-gray-400';
            $backgroundColor = 'bg-gray-700/50';
            $podiumClass = 'hover:md:-translate-y-1'; // Efecto hover sutil para 2 y 3

            if ($rank === 1) {
                $cardColor = 'border-primary';
                $posColor = 'text-primary';
                $backgroundColor = 'bg-primary/20';
                // MEJORA: Clase para elevar y dar brillo al #1
                $podiumClass = 'md:-translate-y-4 shadow-glow hover:md:-translate-y-6';
            } elseif ($rank === 2) {
                $cardColor = 'border-secondary';
                $posColor = 'text-secondary';
                $backgroundColor = 'bg-secondary/20';
            }

        @endphp

        {{-- MEJORA: Añadida transition-transform y la clase $podiumClass --}}
        <div
            class="card p-3 sm:p-4 flex justify-between items-center border-b-4 {{ $cardColor }} shadow-xl {{ $backgroundColor }} transition-all duration-300 {{ $podiumClass }}">

            {{-- SECCIÓN IZQUIERDA: Rank, Info y FOTO --}}
            <div class="flex items-center space-x-3">

                {{-- Rank Destacado --}}
                <div class="text-3xl font-extrabold px-3 py-1 rounded-lg {{ $posColor }} flex-shrink-0">
                    {{ $rank }}
                </div>

                {{-- ⬇️ IMAGEN Y TEXTO ⬇️ --}}
                <a href="{{ route('player.profile', $player->id) }}"
                    class="flex items-center space-x-2 hover:opacity-80 transition duration-150">
                    <img src="{{ $player->foto_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=JUG' }}"
                        onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=JUG'"
                        class="w-10 h-10 rounded-full object-cover flex-shrink-0">

                    <div class="flex flex-col">
                        <p class="text-lg font-bold text-white leading-tight">{{ $player->nombre }}</p>
                        <p class="text-xs text-gray-400">{{ $player->equipo->nombre ?? 'N/A' }}</p>
                    </div>
                </a>
            </div>

            {{-- SECCIÓN DERECHA: Stat Value --}}
            <div class="text-right flex items-center space-x-2">
                {{-- Stat Count --}}
                <div class="p-2 sm:p-3 rounded-lg bg-black/30 flex-shrink-0">
                    <span
                        class="text-3xl font-extrabold {{ $statColor }} block leading-none">{{ $statValue }}</span>
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
@if ($currentList->isNotEmpty())
    {{-- MEJORA: "Glassmorphism" para la tabla --}}
    <div
        class="bg-card-bg/80 backdrop-blur-lg rounded-lg overflow-hidden shadow-xl border border-white/10">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/5 text-sm">
                <thead>
                    {{-- MEJORA: Header de tabla un poco más nítido --}}
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 bg-gray-900/50">
                        <th class="py-3 px-4 w-1/12">Rank</th>
                        <th class="py-3 px-4 w-5/12">Jugador</th>
                        <th class="py-3 px-4 text-center hidden sm:table-cell">PJ</th>
                        <th class="py-3 px-4 text-right">{{ $statName }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($currentList as $player)
                        @php
                            // FIX: Calcular el rank basado en la posición en la colección, sumando el 3 que ya saltamos
                            $rank = $loop->index + 4;
                            $statValue =
                                $activeStat === 'scorers'
                                    ? $player->goles
                                    : ($activeStat === 'assists'
                                        ? $player->asistencias
                                        : $player->paradas);
                            $matchesPlayed = $player->equipo->partidos_jugados ?? 0;
                        @endphp
                        <tr class="hover:bg-white/10 transition duration-150">
                            <td class="py-3 px-4 text-center font-bold text-white/70">{{ $rank }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('player.profile', $player->id) }}"
                                    class="font-medium flex items-center gap-3 hover:text-primary transition duration-150">
                                    <img src="{{ $player->foto_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=JUG' }}"
                                        onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=JUG'"
                                        class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                    <div class="flex flex-col">
                                        <span class="text-white">{{ $player->nombre }}</span>
                                        <span
                                            class="text-xs text-gray-500">{{ $player->equipo->nombre ?? 'N/A' }}</span>
                                    </div>
                                </a>
                            </td>
                            <td class="py-3 px-4 text-center hidden sm:table-cell text-white/70">{{ $matchesPlayed }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold {{ $statColor }}">{{ $statValue }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif