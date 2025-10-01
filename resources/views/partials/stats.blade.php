<h2 class="text-4xl font-bold text-white mb-6 border-b border-green-700 pb-2">Estadísticas de Jugadores</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="card p-6">
        <h3 class="text-xl font-semibold mb-4 text-red-400 flex items-center">Goleadores</h3>
        <ol id="top-scorers" class="list-none space-y-3">
            @forelse($topScorers as $i => $player)
                <li class="flex justify-between items-center text-gray-300">
                    <span class="font-semibold">{{ $i + 1 }}. {{ $player->nombre }} ({{ $player->equipo->nombre ?? 'N/A' }})</span>
                    <span class="text-red-400 font-bold">{{ $player->goles }}</span>
                </li>
            @empty
                <li class="text-gray-500">Aún no hay goles registrados.</li>
            @endforelse
        </ol>
    </div>

    <div class="card p-6">
        <h3 class="text-xl font-semibold mb-4 text-yellow-400 flex items-center">Asistentes</h3>
        <ol id="top-assists" class="list-none space-y-3">
            @forelse($topAssists as $i => $player)
                <li class="flex justify-between items-center text-gray-300">
                    <span class="font-semibold">{{ $i + 1 }}. {{ $player->nombre }} ({{ $player->equipo->nombre ?? 'N/A' }})</span>
                    <span class="text-yellow-400 font-bold">{{ $player->asistencias }}</span>
                </li>
            @empty
                <li class="text-gray-500">Aún no hay asistencias registradas.</li>
            @endforelse
        </ol>
    </div>

    <div class="card p-6">
        <h3 class="text-xl font-semibold mb-4 text-blue-400 flex items-center">Mejores Porteros</h3>
        <ol id="top-keepers" class="list-none space-y-3">
            @forelse($topKeepers as $i => $player)
                <li class="flex justify-between items-center text-gray-300">
                    <span class="font-semibold">{{ $i + 1 }}. {{ $player->nombre }} ({{ $player->equipo->nombre ?? 'N/A' }})</span>
                    <span class="text-blue-400 font-bold">{{ $player->paradas }}</span>
                </li>
            @empty
                <li class="text-gray-500">Aún no hay porteros con paradas registradas.</li>
            @endforelse
        </ol>
    </div>
</div>