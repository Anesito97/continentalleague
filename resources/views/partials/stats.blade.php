<h2 class="text-4xl font-bold text-white mb-6 border-b border-green-700 pb-2">Estadísticas de Jugadores</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    {{-- GOLEADORES --}}
    <div class="card p-6">
        <h3 class="text-xl font-semibold mb-4 text-red-400 flex items-center">
            {{-- Puedes añadir un icono SVG aquí si quieres --}}
            Goleadores
        </h3>
        <ol id="top-scorers" class="list-none space-y-3">
            @forelse($topScorers as $i => $player)
                <li class="flex justify-between items-center text-gray-300 border-b border-gray-700 pb-3 last:border-b-0">
                    
                    {{-- Player Info (Photo, Rank, Name, Team) --}}
                    <div class="flex items-center space-x-3">
                        <span class="font-bold w-4 text-center {{ $i === 0 ? 'text-yellow-400' : '' }}">{{ $i + 1 }}.</span>
                        
                        {{-- ⬇️ Imagen del Jugador ⬇️ --}}
                        <img src="{{ $player->foto_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=JUG' }}"
                            onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=JUG'"
                            class="w-10 h-10 rounded-full object-cover">
                        
                        <div class="flex flex-col text-sm">
                            <span class="font-semibold text-white">{{ $player->nombre }}</span>
                            <span class="text-xs text-gray-400">{{ $player->equipo->nombre ?? 'N/A' }}</span>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="text-right">
                        <span class="text-red-400 font-bold text-xl">{{ $player->goles }}</span>
                        <span class="text-xs text-gray-500 block">Goles</span>
                    </div>
                </li>
            @empty
                <li class="text-gray-500">Aún no hay goles registrados.</li>
            @endforelse
        </ol>
    </div>

    {{-- ASISTENTES --}}
    <div class="card p-6">
        <h3 class="text-xl font-semibold mb-4 text-yellow-400 flex items-center">Asistentes</h3>
        <ol id="top-assists" class="list-none space-y-3">
            @forelse($topAssists as $i => $player)
                <li class="flex justify-between items-center text-gray-300 border-b border-gray-700 pb-3 last:border-b-0">
                    <div class="flex items-center space-x-3">
                        <span class="font-bold w-4 text-center {{ $i === 0 ? 'text-yellow-400' : '' }}">{{ $i + 1 }}.</span>
                        
                        <img src="{{ $player->foto_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=JUG' }}"
                            onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=JUG'"
                            class="w-10 h-10 rounded-full object-cover">
                        
                        <div class="flex flex-col text-sm">
                            <span class="font-semibold text-white">{{ $player->nombre }}</span>
                            <span class="text-xs text-gray-400">{{ $player->equipo->nombre ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <span class="text-yellow-400 font-bold text-xl">{{ $player->asistencias }}</span>
                        <span class="text-xs text-gray-500 block">Asist.</span>
                    </div>
                </li>
            @empty
                <li class="text-gray-500">Aún no hay asistencias registradas.</li>
            @endforelse
        </ol>
    </div>

    {{-- MEJORES PORTEROS --}}
    <div class="card p-6">
        <h3 class="text-xl font-semibold mb-4 text-blue-400 flex items-center">Mejores Porteros</h3>
        <ol id="top-keepers" class="list-none space-y-3">
            @forelse($topKeepers as $i => $player)
                <li class="flex justify-between items-center text-gray-300 border-b border-gray-700 pb-3 last:border-b-0">
                    <div class="flex items-center space-x-3">
                        <span class="font-bold w-4 text-center {{ $i === 0 ? 'text-yellow-400' : '' }}">{{ $i + 1 }}.</span>
                        
                        <img src="{{ $player->foto_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=JUG' }}"
                            onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=JUG'"
                            class="w-10 h-10 rounded-full object-cover">
                        
                        <div class="flex flex-col text-sm">
                            <span class="font-semibold text-white">{{ $player->nombre }}</span>
                            <span class="text-xs text-gray-400">{{ $player->equipo->nombre ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <span class="text-blue-400 font-bold text-xl">{{ $player->paradas }}</span>
                        <span class="text-xs text-gray-500 block">Paradas</span>
                    </div>
                </li>
            @empty
                <li class="text-gray-500">Aún no hay porteros con paradas registradas.</li>
            @endforelse
        </ol>
    </div>
</div>