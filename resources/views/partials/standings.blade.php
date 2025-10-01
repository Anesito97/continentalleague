<h2 class="text-4xl font-bold text-white mb-6 border-b border-green-700 pb-2">Tabla de Posiciones</h2>

<div class="card p-4 hover:transform-none">
    <h3 class="text-xl font-semibold mb-3 text-green-400">Próximos Partidos</h3>
    <div id="recent-matches" class="space-y-3">
        @forelse($recentMatches->where('estado', 'pendiente')->take(5) as $match)
            <div class="p-2 border-l-4 border-green-500 bg-gray-700 rounded-md flex justify-between items-center text-sm">
                <span>{{ $match->localTeam->nombre }} <span class="font-bold text-green-400">vs</span> {{ $match->visitorTeam->nombre }}</span>
                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($match->fecha_hora)->locale('es')->isoFormat('ddd, D MMM, HH:mm') }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-400">No hay partidos pendientes.</p>
        @endforelse
    </div>
</div>

<div class="overflow-x-auto card p-6 hover:transform-none">
    <table class="min-w-full divide-y divide-gray-700">
        <thead>
            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                <th class="py-3 px-2">#</th>
                <th class="py-3 px-2">Equipo</th>
                <th class="py-3 px-2 text-center">Ptos</th>
                <th class="py-3 px-2 text-center">PJ</th>
                <th class="py-3 px-2 text-center">G</th>
                <th class="py-3 px-2 text-center hidden sm:table-cell">E</th>
                <th class="py-3 px-2 text-center hidden sm:table-cell">P</th>
                <th class="py-3 px-2 text-center hidden sm:table-cell">GF</th>
                <th class="py-3 px-2 text-center hidden sm:table-cell">GC</th>
            </tr>
        </thead>
        <tbody id="standings-body" class="divide-y divide-gray-800 text-sm">
            @forelse($teams as $index => $team)
                <tr class="hover:bg-gray-700 transition">
                    <td class="py-3 px-2 font-bold {{ $index === 0 ? 'text-yellow-400' : 'text-gray-300' }}">{{ $index + 1 }}</td>
                    <td class="py-3 px-2 flex items-center">
                        <img src="{{ $team->escudo_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=LOGO' }}" 
                            onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=LOGO'"
                            class="w-8 h-8 rounded-full object-cover mr-3" />
                        <span class="font-medium text-white">{{ $team->nombre }}</span>
                    </td>
                    <td class="py-3 px-2 text-center font-bold text-green-400">{{ $team->puntos }}</td>
                    <td class="py-3 px-2 text-center">{{ $team->partidos_jugados }}</td>
                    <td class="py-3 px-2 text-center">{{ $team->ganados }}</td>
                    <td class="py-3 px-2 text-center hidden sm:table-cell">{{ $team->empatados }}</td>
                    <td class="py-3 px-2 text-center hidden sm:table-cell">{{ $team->perdidos }}</td>
                    <td class="py-3 px-2 text-center hidden sm:table-cell">{{ $team->goles_a_favor }}</td>
                    <td class="py-3 px-2 text-center hidden sm:table-cell">{{ $team->goles_en_contra }}</td>
                </tr>
            @empty
                <tr class="hover:bg-gray-700 transition"><td colspan="9" class="py-4 text-center text-gray-500">Aún no hay equipos.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>