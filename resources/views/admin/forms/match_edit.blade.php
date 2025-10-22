<div class="space-y-6 bg-gray-800 p-6 rounded-lg border border-gray-700 shadow-lg">

    <fieldset class="border border-gray-600 rounded-lg p-4">
        <legend class="px-2 text-lg font-semibold text-gray-300">Detalles del Partido</legend>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2">
            <div>
                <label for="match-jornada" class="block text-sm font-medium text-gray-400 mb-1">Jornada Nº</label>
                <input type="number" name="jornada" id="match-jornada" required min="1"
                    value="{{ old('jornada', $match->jornada) }}"
                    class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
            </div>

            <div>
                <label for="match-date" class="block text-sm font-medium text-gray-400 mb-1">Fecha</label>
                <input type="date" name="date" id="match-date"
                    value="{{ old('date', \Carbon\Carbon::parse($match->fecha_hora)->format('Y-m-d')) }}" required
                    class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
            </div>

            <div>
                <label for="match-time" class="block text-sm font-medium text-gray-400 mb-1">Hora</label>
                <input type="time" name="time" id="match-time"
                    value="{{ old('time', \Carbon\Carbon::parse($match->fecha_hora)->format('H:i')) }}" required
                    class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
            </div>
        </div>
    </fieldset>

    <fieldset class="border border-gray-600 rounded-lg p-4">
        <legend class="px-2 text-lg font-semibold text-gray-300">Enfrentamiento</legend>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-2">
            <div>
                <label for="local-team" class="block text-sm font-medium text-gray-400 mb-1">Equipo Local</label>
                <select name="localId" id="local-team" required 
                    class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    <option value="">Seleccionar Local...</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" @selected(old('localId', $match->equipo_local_id) == $team->id)>{{ $team->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="visitor-team" class="block text-sm font-medium text-gray-400 mb-1">Equipo Visitante</label>
                <select name="visitorId" id="visitor-team" required 
                    class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    <option value="">Seleccionar Visitante...</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" @selected(old('visitorId', '!=', $match->equipo_local_id) && old('visitorId', $match->equipo_visitante_id) == $team->id)>{{ $team->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </fieldset>
</div>