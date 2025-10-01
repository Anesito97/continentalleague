<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-400">Fecha</label>
        <input type="date" name="date" value="{{ \Carbon\Carbon::parse($match->fecha_hora)->format('Y-m-d') }}" required class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-400">Hora</label>
        <input type="time" name="time" value="{{ \Carbon\Carbon::parse($match->fecha_hora)->format('H:i') }}" required class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white">
    </div>
</div>

<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-400">Equipo Local</label>
        <select name="localId" required class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white">
            <option value="">Seleccionar Local...</option>
            @foreach($teams as $team)
                <option value="{{ $team->id }}" @selected($match->equipo_local_id == $team->id)>{{ $team->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-400">Equipo Visitante</label>
        <select name="visitorId" required class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white">
            <option value="">Seleccionar Visitante...</option>
            @foreach($teams as $team)
                <option value="{{ $team->id }}" @selected($match->equipo_visitante_id == $team->id)>{{ $team->nombre }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- Campo oculto para combinar fecha y hora en el controlador --}}
<input type="hidden" name="dateTime" value="">