{{-- Este formulario ahora maneja AMBOS casos: pendiente y finalizado --}}

{{-- ========================================================== --}}
{{-- PARTE 1: Formulario para Partido PENDIENTE --}}
{{-- ========================================================== --}}
@if ($match->estado === 'pendiente')
    <div class="space-y-6 bg-gray-800 p-6 rounded-lg border border-gray-700 shadow-lg">
        <fieldset class="border border-gray-600 rounded-lg p-4">
            <legend class="px-2 text-lg font-semibold text-gray-300">Detalles del Partido</legend>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2">
                <div>
                    <label for="match-jornada" class="block text-sm font-medium text-gray-400 mb-1">Jornada Nº</label>
                    <input type="number" name="jornada" id="match-jornada" required min="1"
                        value="{{ old('jornada', $match->jornada) }}"
                        class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                </div>
                <div>
                    <label for="match-date" class="block text-sm font-medium text-gray-400 mb-1">Fecha</label>
                    <input type="date" name="date" id="match-date"
                        value="{{ old('date', \Carbon\Carbon::parse($match->fecha_hora)->format('Y-m-d')) }}" required
                        class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                </div>
                <div>
                    <label for="match-time" class="block text-sm font-medium text-gray-400 mb-1">Hora</label>
                    <input type="time" name="time" id="match-time"
                        value="{{ old('time', \Carbon\Carbon::parse($match->fecha_hora)->format('H:i')) }}" required
                        class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                </div>
            </div>
        </fieldset>
        <fieldset class="border border-gray-600 rounded-lg p-4">
            <legend class="px-2 text-lg font-semibold text-gray-300">Enfrentamiento</legend>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-2">
                <div>
                    <label for="local-team" class="block text-sm font-medium text-gray-400 mb-1">Equipo Local</label>
                    <select name="localId" id="local-team" required 
                        class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                        <option value="">Seleccionar Local...</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" @selected(old('localId', $match->equipo_local_id) == $team->id)>{{ $team->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="visitor-team" class="block text-sm font-medium text-gray-400 mb-1">Equipo Visitante</label>
                    <select name="visitorId" id="visitor-team" required 
                        class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                        <option value="">Seleccionar Visitante...</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" @selected(old('visitorId', '!=', $match->equipo_visitante_id) && old('visitorId', $match->equipo_visitante_id) == $team->id)>{{ $team->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </fieldset>
    </div>

{{-- ========================================================== --}}
{{-- PARTE 2: Formulario para Partido FINALIZADO --}}
{{-- ========================================================== --}}
@else
    <div class="space-y-8">
        <div class="text-sm p-3 rounded-lg bg-yellow-800/50 border border-yellow-500 text-yellow-300">
            <p><span class="font-bold">Modo de Edición de Partido Finalizado:</span> Estás editando un partido que ya fue finalizado. Cualquier cambio aquí **revertirá y recalculará** automáticamente todas las estadísticas de la liga.</p>
        </div>

        <fieldset class="border border-gray-700 rounded-lg p-4">
            <legend class="px-2 text-lg font-semibold text-gray-300">Editar Marcador</legend>
            <div class="flex items-center justify-center gap-4 sm:gap-8 mt-4 text-center">
                <div class="flex-1">
                    <img src="{{ $match->localTeam->escudo_url ?? '' }}" class="w-16 h-16 rounded-full mx-auto mb-2 border-2 border-primary/50">
                    <label class="font-bold text-lg text-white">{{ $match->localTeam->nombre }}</label>
                    {{-- ✅ DATOS PRE-CARGADOS --}}
                    <input type="number" name="goles_local" value="{{ old('goles_local', $match->goles_local) }}" min="0" required
                        class="mt-2 block w-full text-4xl font-bold bg-gray-800 border-2 border-gray-600 rounded-md text-green-400 text-center focus:ring-green-500 focus:border-green-500">
                </div>
                <span class="text-4xl font-black text-gray-500 pt-24">VS</span>
                <div class="flex-1">
                    <img src="{{ $match->visitorTeam->escudo_url ?? '' }}" class="w-16 h-16 rounded-full mx-auto mb-2 border-2 border-secondary/50">
                    <label class="font-bold text-lg text-white">{{ $match->visitorTeam->nombre }}</label>
                    {{-- ✅ DATOS PRE-CARGADOS --}}
                    <input type="number" name="goles_visitor" value="{{ old('goles_visitor', $match->goles_visitante) }}" min="0" required
                        class="mt-2 block w-full text-4xl font-bold bg-gray-800 border-2 border-gray-600 rounded-md text-white text-center focus:ring-green-500 focus:border-green-500">
                </div>
            </div>
        </fieldset>
        
        <fieldset class="border border-gray-700 rounded-lg p-4">
            <legend class="px-2 text-lg font-semibold text-gray-300">Editar Eventos</legend>
            <div id="events-container" class="space-y-4 mt-4">
                {{-- ✅ Bucle sobre los eventos EXISTENTES --}}
                @forelse($match->eventos as $i => $event)
                    @include('admin.partials.event_row_edit', ['i' => $i, 'event' => $event, 'localTeam' => $match->localTeam, 'visitorTeam' => $match->visitorTeam])
                @empty
                    <p class="text-center text-gray-500 text-sm">No se registraron eventos para este partido.</p>
                @endforelse

                {{-- Añadimos 2 filas vacías al final para añadir nuevos eventos --}}
                @php $startIndex = $match->eventos->count(); @endphp
                @for ($i = $startIndex; $i < $startIndex + 2; $i++)
                    @include('admin.partials.event_row_edit', ['i' => $i, 'event' => null, 'localTeam' => $match->localTeam, 'visitorTeam' => $match->visitorTeam])
                @endfor
            </div>
            <button type="button" id="add-event-btn" class="mt-4 w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
                <i class="fa-solid fa-plus mr-2"></i> Añadir Otro Evento
            </button>
        </fieldset>
    </div>
@endif