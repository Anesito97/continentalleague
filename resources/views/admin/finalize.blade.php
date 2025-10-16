<div id="finalize-match-content" class="admin-content space-y-6">
    <h4 class="text-2xl font-semibold mb-4 text-green-400">Finalizar Partido & Registrar Eventos</h4>
    <div class="card p-6">
        <div class="mb-4">
            <label for="match-to-finalize" class="block text-sm font-medium text-gray-400 mb-2">Selecciona Partido a Finalizar</label>
            
            {{-- Usamos la selección del partido para cargar los datos en el siguiente paso --}}
            <form method="GET" action="{{ route('admin.finalize-match') }}">
                <select id="match-to-finalize" name="match_id" onchange="this.form.submit()"
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
                    <option value="">Selecciona Partido...</option>
                    @foreach($pendingMatches as $match)
                        <option value="{{ $match->id }}" @selected(request('match_id') == $match->id)>
                            (Jornada {{ $match->jornada }}) - {{ $match->localTeam->nombre }} vs {{ $match->visitorTeam->nombre }} ({{ \Carbon\Carbon::parse($match->fecha_hora)->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Mostrar formulario si hay un partido seleccionado --}}
        @php
            $selectedMatch = $pendingMatches->firstWhere('id', request('match_id'));
        @endphp

        @if($selectedMatch)
            <form id="finalize-match-form" method="POST" action="{{ route('matches.finalize') }}">
                @csrf
                <input type="hidden" name="match_id" value="{{ $selectedMatch->id }}">

                <h5 class="text-xl font-bold mb-4 text-center">
                    <span class="text-green-400">{{ $selectedMatch->localTeam->nombre }}</span> vs <span>{{ $selectedMatch->visitorTeam->nombre }}</span>
                </h5>

                <div class="grid grid-cols-2 gap-4 mb-6 text-center">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Goles Local</label>
                        <input type="number" name="goles_local" value="{{ old('goles_local', 0) }}" min="0" required
                            class="mt-1 block w-full px-3 py-2 text-3xl font-bold bg-gray-700 border border-gray-600 rounded-md text-green-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Goles Visitante</label>
                        <input type="number" name="goles_visitor" value="{{ old('goles_visitor', 0) }}" min="0" required
                            class="mt-1 block w-full px-3 py-2 text-3xl font-bold bg-gray-700 border border-gray-600 rounded-md text-white">
                    </div>
                </div>

                {{-- REGISTRO DE EVENTOS (Fijo: 5 Filas) --}}
                <div class="mb-6 border-t border-gray-700 pt-4">
                    <h6 class="text-lg font-semibold mb-3 text-white">Detalle de Eventos (Máximo 20)</h6>
                    <p class="text-xs text-gray-400 mb-3" style="color: red;">Si una fila se deja vacía, será ignorada por el servidor.</p>
                    
                    <div id="events-container" class="space-y-3">
                        @for ($i = 0; $i < 20; $i++)
                            <div class="flex flex-col sm:flex-row gap-2 card p-3 border border-gray-600 transition duration-150">
                                <select name="events[{{ $i }}][event_type]" class="w-full sm:w-1/3 px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm">
                                    <option value="">Tipo de Evento...</option>
                                    @foreach(['Gol', 'Asistencia', 'Parada', 'Amarilla', 'Roja'] as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                                <select name="events[{{ $i }}][player_id]" class="w-full sm:flex-grow px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm">
                                    <option value="">Seleccionar Jugador...</option>
                                    @foreach($players as $player)
                                        <option value="{{ $player->id }}">
                                            {{ $player->nombre }} (#{{ $player->numero }}) - {{ $player->equipo->nombre ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="number" name="events[{{ $i }}][minuto]" placeholder="Minuto" class="w-16 px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm">
                            </div>
                        @endfor
                    </div>
                </div>

                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition w-full">
                    Finalizar y Calcular Puntos 
                </button>
            </form>
        @else
            <p id="finalize-match-info" class="text-center text-gray-500 mt-4">
                Selecciona un partido pendiente en el menú desplegable de arriba para registrar los resultados.
            </p>
        @endif
    </div>
</div>