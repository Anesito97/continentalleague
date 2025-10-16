<div id="matches-content" class="admin-content space-y-6">

    {{-- FORMULARIO PARA PROGRAMAR PARTIDO --}}
    <div class="card p-6">
        <h4 class="text-2xl font-semibold mb-4 text-green-400">Programar Partido</h4>
        <form method="POST" action="{{ route('matches.store') }}">
            @csrf
            <div class="mb-4">
                <label for="match-jornada" class="block text-sm font-medium text-gray-400">Jornada Nº</label>
                <input type="number" name="jornada" id="match-jornada" required min="1" value="{{ old('jornada', 1) }}"
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="match-local" class="block text-sm font-medium text-gray-400">Equipo Local</label>
                    <select id="match-local" name="localId" required
                        class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
                        <option value="">Seleccionar Local...</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" @selected(old('localId') == $team->id)>{{ $team->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="match-visitor" class="block text-sm font-medium text-gray-400">Equipo Visitante</label>
                    <select id="match-visitor" name="visitorId" required
                        class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
                        <option value="">Seleccionar Visitante...</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" @selected(old('visitorId') == $team->id)>{{ $team->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="match-date" class="block text-sm font-medium text-gray-400">Fecha</label>
                    <input type="date" id="match-date" name="date" required
                        class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
                </div>
                <div>
                    <label for="match-time" class="block text-sm font-medium text-gray-400">Hora</label>
                    <input type="time" id="match-time" name="time" required
                        class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
                </div>
            </div>
            {{-- ELIMINAMOS EL CAMPO OCULTO 'dateTime' y el JS asociado, el controlador usa 'date' y 'time' --}}

            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">Programar
                </button>
        </form>
    </div>

    {{-- LISTADO DE PARTIDOS PENDIENTES CON ACCIONES --}}
    <div id="matches-content" class="admin-content space-y-6">
        {{-- ... (Formulario de Programación) ... --}}

        {{-- LISTADO DE PARTIDOS PENDIENTES CON ACCIONES --}}
        <div class="card p-4 hover:transform-none">
            <h4 class="text-xl font-semibold mb-3">Partidos Pendientes</h4>
            <ul id="pending-matches" class="space-y-2 text-sm text-gray-300">
                @forelse($pendingMatches as $match)
                    <li class="p-2 bg-gray-700 rounded-md flex justify-between items-center">
                        <span class="font-medium">{{ $match->localTeam->nombre }} vs
                            {{ $match->visitorTeam->nombre }}</span>
                        <span
                            class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($match->fecha_hora)->format('d/m/Y H:i') }}</span>

                        {{-- ACCIONES --}}
                        <div class="flex space-x-2">
                            <a href="{{ route('matches.edit', $match->id) }}"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded-md text-xs">Editar</a>

                            <form method="POST" action="{{ route('matches.destroy', $match->id) }}"
                                onsubmit="return confirm('¿Seguro que quieres eliminar el partido {{ $match->localTeam->nombre }} vs {{ $match->visitorTeam->nombre }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-md text-xs">Eliminar</button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="p-2 text-center text-gray-500">No hay partidos pendientes.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
