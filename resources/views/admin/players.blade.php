<div id="players-content" class="admin-content space-y-6">
    <div class="card p-6">
        <h4 class="text-2xl font-semibold mb-4 text-green-400">Registrar Jugador</h4>
        <form method="POST" action="{{ route('players.store') }}" enctype="multipart/form-data">
            @csrf
            {{-- Nombre y Número --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="player-name" class="block text-sm font-medium text-gray-400">Nombre</label>
                    <input type="text" name="name" id="player-name" required value="{{ old('name') }}"
                        class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
                </div>
                <div>
                    <label for="player-number" class="block text-sm font-medium text-gray-400">Número de
                        Camiseta</label>
                    <input type="number" name="number" id="player-number" required min="1" max="99"
                        value="{{ old('number') }}"
                        class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
                </div>
            </div>

            {{-- Equipo y Posición --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="player-team" class="block text-sm font-medium text-gray-400">Equipo</label>
                    <select id="player-team" name="teamId" required
                        class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
                        <option value="">Seleccionar Equipo...</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" @selected(old('teamId') == $team->id)>{{ $team->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="player-position" class="block text-sm font-medium text-gray-400">Posición</label>
                    <input type="hidden" name="posicion_general" id="posicion_general_input">

                    <select id="player-position" name="posicion_especifica" required
                        class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
                        <option value="">Seleccionar Posición...</option>
                        @foreach ($positions as $general => $specifics)
                            <optgroup label="{{ $general }}">
                                @foreach ($specifics as $abbr => $name)
                                    <option value="{{ $abbr }}" data-general="{{ strtolower($general) }}"
                                        @selected(old('posicion_especifica') == $abbr)>
                                        {{ $name }} ({{ $abbr }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Foto --}}
            <div class="mb-4">
                <label for="player-photo" class="block text-sm font-medium text-gray-400">Foto del Jugador
                    (Opcional)</label>
                <p class="text-xs text-gray-500 mb-1">La foto debe subirse al servidor.</p>
                <input type="file" name="photo" id="player-photo" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            </div>

            <button type="submit" id="save-player-btn"
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">Guardar
                Jugador </button>
        </form>
    </div>

    {{-- Listado de Jugadores --}}
    <div class="card p-4 hover:transform-none">
        <h4 class="text-xl font-semibold mb-3">Últimos 10 Jugadores Registrados</h4>
        <ul id="players-list" class="space-y-2 text-sm text-gray-300">
            @forelse($players as $player)
                {{-- Contenido principal de la lista --}}
                <li class="p-2 bg-gray-700 rounded-md flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <img src="{{ $player->foto_url ?? 'https://placehold.co/100x100/1f2937/FFFFFF?text=JUGADOR' }}"
                            onerror="this.src='https://placehold.co/100x100/1f2937/FFFFFF?text=JUGADOR'"
                            class="w-8 h-8 rounded-full object-cover">
                        <span>{{ $player->nombre }} (#{{ $player->numero }})</span>
                    </div>

                    {{-- ✅ LÍNEA MODIFICADA AQUÍ 👇 --}}
                    <span class="text-xs text-gray-400">
                        {{ $player->equipo->nombre ?? 'N/A' }} -
                        {{ ucfirst($player->posicion_general) }} ({{ strtoupper($player->posicion_especifica) }})
                    </span>

                    {{-- ACCIONES DE EDITAR/ELIMINAR --}}
                    <div class="flex space-x-2">
                        <a href="{{ route('players.edit', $player->id) }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded-md text-xs">Editar</a>

                        <form method="POST" action="{{ route('players.destroy', $player->id) }}"
                            onsubmit="return confirm('¿Seguro que quieres eliminar a {{ $player->nombre }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-md text-xs">Eliminar</button>
                        </form>
                    </div>
                </li>
            @empty
                <li class="p-2 text-center text-gray-500">No hay jugadores registrados.</li>
            @endforelse
        </ul>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const positionSelect = document.getElementById('player-position');
        const generalPositionInput = document.getElementById('posicion_general_input');

        // Función para actualizar el campo oculto
        function updateGeneralPosition() {
            const selectedOption = positionSelect.options[positionSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                generalPositionInput.value = selectedOption.dataset.general;
            } else {
                generalPositionInput.value = '';
            }
        }

        // Llama a la función al cargar la página (importante para el form de edición)
        updateGeneralPosition();

        // Y añade un listener para cuando cambie la selección
        positionSelect.addEventListener('change', updateGeneralPosition);
    });
</script>
