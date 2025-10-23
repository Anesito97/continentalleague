<div class="event-row flex flex-col sm:flex-row gap-2 bg-gray-800 p-3 rounded-lg border border-gray-600">
    <select name="events[{{ $i }}][event_type]" class="event-type-select w-full sm:w-1/4 px-3 py-2 bg-gray-700 border-gray-600 rounded-md text-sm">
        <option value="">Tipo de Evento...</option>
        <option value="Gol">⚽ Gol</option>
        <option value="Asistencia">👟 Asistencia</option>
        <option value="Parada">🧤 Parada</option>
        <option value="Amarilla">🟨 Amarilla</option>
        <option value="Roja">🟥 Roja</option>
    </select>
    
    <select name="events[{{ $i }}][goal_type]" class="goal-type-select hidden w-full sm:w-1/4 px-3 py-2 bg-gray-700 border-gray-600 rounded-md text-sm">
        <option value="Jugada">De Jugada</option>
        <option value="Cabeza">De Cabeza</option>
        <option value="Penalti">De Penalti</option>
        <option value="Tiro Libre">De Tiro Libre</option>
        <option value="En Contra">En Contra</option>
    </select>

    <select name="events[{{ $i }}][player_id]" class="w-full sm:flex-grow px-3 py-2 bg-gray-700 border-gray-600 rounded-md text-sm">
        <option value="">Seleccionar Jugador...</option>
        <optgroup label="{{ $localTeam->nombre }}">
            @foreach($localTeam->jugadores as $player)
                <option value="{{ $player->id }}">{{ $player->nombre }} (#{{ $player->numero }})</option>
            @endforeach
        </optgroup>
        <optgroup label="{{ $visitorTeam->nombre }}">
            @foreach($visitorTeam->jugadores as $player)
                <option value="{{ $player->id }}">{{ $player->nombre }} (#{{ $player->numero }})</option>
            @endforeach
        </optgroup>
    </select>
    
    <input type="number" name="events[{{ $i }}][minuto]" placeholder="Min'" min="1" max="120" class="w-full sm:w-16 px-3 py-2 bg-gray-700 border-gray-600 rounded-md text-sm text-center">
    <button type="button" class="remove-event-btn bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded-md text-xs">
        <i class="fa-solid fa-trash-can"></i>
    </button>
</div>