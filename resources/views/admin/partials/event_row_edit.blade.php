@php
    // $event es el evento existente (o null si es una fila nueva)
    $i = $i ?? 0; // Índice
    $selectedType = old("events.$i.event_type", $event->tipo_evento ?? '');
    $selectedGoalType = old("events.$i.goal_type", $event->goal_type ?? 'Jugada');
    $selectedPlayer = old("events.$i.player_id", $event->jugador_id ?? '');
    $selectedMinute = old("events.$i.minuto", $event->minuto ?? '');
@endphp

<div class="event-row flex flex-col sm:flex-row gap-2 bg-gray-800 p-3 rounded-lg border border-gray-600">
    <select name="events[{{ $i }}][event_type]" class="event-type-select w-full sm:w-1/4 px-3 py-2 bg-gray-700 border-gray-600 rounded-md text-sm">
        <option value="">Tipo de Evento...</option>
        <option value="Gol" @selected($selectedType == 'gol')>⚽ Gol</option>
        <option value="Asistencia" @selected($selectedType == 'asistencia')>👟 Asistencia</option>
        <option value="Parada" @selected($selectedType == 'parada')>🧤 Parada</option>
        <option value="Amarilla" @selected($selectedType == 'amarilla')>🟨 Amarilla</option>
        <option value="Roja" @selected($selectedType == 'roja')>🟥 Roja</option>
    </select>
    
    <select name="events[{{ $i }}][goal_type]" class="goal-type-select @if($selectedType !== 'gol') hidden @endif w-full sm:w-1/4 px-3 py-2 bg-gray-700 border-gray-600 rounded-md text-sm">
        <option value="Jugada" @selected($selectedGoalType == 'jugada')>De Jugada</option>
        <option value="Cabeza" @selected($selectedGoalType == 'cabeza')>De Cabeza</option>
        <option value="Penalti" @selected($selectedGoalType == 'penalti')>De Penalti</option>
        <option value="Tiro Libre" @selected($selectedGoalType == 'tiro libre')>De Tiro Libre</option>
        <option value="En Contra" @selected($selectedGoalType == 'en contra')>En Contra</option>
    </select>

    <select name="events[{{ $i }}][player_id]" class="w-full sm:flex-grow px-3 py-2 bg-gray-700 border-gray-600 rounded-md text-sm">
        <option value="">Seleccionar Jugador...</option>
        <optgroup label="{{ $localTeam->nombre }}">
            @foreach($localTeam->jugadores as $player)
                <option value="{{ $player->id }}" @selected($selectedPlayer == $player->id)>{{ $player->nombre }} (#{{ $player->numero }})</option>
            @endforeach
        </optgroup>
        <optgroup label="{{ $visitorTeam->nombre }}">
            @foreach($visitorTeam->jugadores as $player)
                <option value="{{ $player->id }}" @selected($selectedPlayer == $player->id)>{{ $player->nombre }} (#{{ $player->numero }})</option>
            @endforeach
        </optgroup>
    </select>
    
    <input type="number" name="events[{{ $i }}][minuto]" value="{{ $selectedMinute }}" placeholder="Min'" min="1" max="120" class="w-full sm:w-16 px-3 py-2 bg-gray-700 border-gray-600 rounded-md text-sm text-center">
    <button type="button" class="remove-event-btn bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded-md text-xs">
        <i class="fa-solid fa-trash-can"></i>
    </button>
</div>