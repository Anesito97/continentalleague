<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-400">Nombre</label>
        <input type="text" name="nombre" value="{{ $player->nombre }}" required class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-400">Número</label>
        <input type="number" name="numero" value="{{ $player->numero }}" required min="1" max="99" class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white">
    </div>
</div>

<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-400">Equipo</label>
        <select name="equipo_id" required class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white">
            <option value="">Seleccionar Equipo...</option>
            @foreach($teams as $team)
                <option value="{{ $team->id }}" @selected($player->equipo_id == $team->id)>{{ $team->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-400">Posición</label>
        <select name="posicion" required class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white">
            @foreach(['portero', 'defensa', 'medio', 'delantero'] as $pos)
                <option value="{{ $pos }}" @selected($player->posicion == $pos)>{{ ucfirst($pos) }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="border-t border-gray-600 pt-3">
    <label class="block text-sm font-medium text-gray-400">Foto Actual</label>
    <img src="{{ $player->foto_url }}" class="w-16 h-16 rounded-full object-cover my-2">
    <label class="block text-sm font-medium text-gray-400">Subir Nueva Foto (Sustituir)</label>
    <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-300">
</div>