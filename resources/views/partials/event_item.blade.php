@php
    $iconClass = '';
    $textColor = 'text-gray-400';
    $player = $event->jugador;
    
    switch ($event->tipo_evento) {
        case 'gol':
            $iconClass = '⚽';
            $textColor = 'text-red-400';
            break;
        case 'asistencia':
            $iconClass = '👟';
            $textColor = 'text-yellow-400';
            break;
        case 'amarilla':
            $iconClass = '🟨';
            $textColor = 'text-yellow-300';
            break;
        case 'roja':
            $iconClass = '🟥';
            $textColor = 'text-red-600';
            break;
        case 'parada':
            $iconClass = '🧤';
            $textColor = 'text-blue-400';
            break;
    }
    
    // Nombre del jugador acortado si es muy largo
    $playerName = $player->nombre ?? 'N/A';
    $shortName = strlen($playerName) > 15 ? substr($playerName, 0, 12) . '...' : $playerName;
@endphp

@if($align === 'right')
    {{-- Alineación DERECHA (Para el equipo Local) --}}
    <div class="flex justify-end items-center text-right">
        <span class="font-bold text-gray-300 mr-1">{{ $event->minuto }}'</span>
        <span class="flex-grow text-ellipsis overflow-hidden whitespace-nowrap text-gray-300">
            {{ $shortName }}
        </span>
        <span class="font-semibold {{ $textColor }} ml-1">{{ $iconClass }}</span>
    </div>
@else
    {{-- Alineación IZQUIERDA (Para el equipo Visitante) --}}
    <div class="flex justify-start items-center text-left">
        <span class="font-semibold {{ $textColor }} mr-1">{{ $iconClass }}</span>
        <span class="flex-grow text-ellipsis overflow-hidden whitespace-nowrap text-gray-300">
            {{ $shortName }}
        </span>
        <span class="font-bold text-gray-300 ml-1">{{ $event->minuto }}'</span>
    </div>
@endif