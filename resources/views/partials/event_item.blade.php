@php
    $iconClass = '';
    $textColor = 'text-gray-400';
    $player = $event->jugador;
    
    switch ($event->tipo_evento) {
        case 'gol':
            $textColor = 'text-red-400';
            
            // ✅ CORRECCIÓN: Usamos strtolower() para que no importe si es mayúscula o minúscula
            $iconClass = match (strtolower($event->goal_type ?? '')) {
                'penalti'    => '🥅',
                'en contra'  => '🤦',
                default      => '⚽',
            };
            break;
        
        // ... (los otros 'case' no cambian) ...
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
    
    $playerName = $player->nombre ?? 'N/A';
    $shortName = strlen($playerName) > 15 ? substr($playerName, 0, 12) . '...' : $playerName;
@endphp

{{-- La estructura HTML no cambia --}}
@if($align === 'right')
    <div class="flex justify-end items-center text-right">
        <span class="font-bold text-gray-300 mr-1">{{ $event->minuto }}'</span>
        <span class="flex-grow text-ellipsis overflow-hidden whitespace-nowrap text-gray-300">{{ $shortName }}</span>
        <span class="font-semibold {{ $textColor }} ml-1">{{ $iconClass }}</span>
    </div>
@else
    <div class="flex justify-start items-center text-left">
        <span class="font-semibold {{ $textColor }} mr-1">{{ $iconClass }}</span>
        <span class="flex-grow text-ellipsis overflow-hidden whitespace-nowrap text-gray-300">{{ $shortName }}</span>
        <span class="font-bold text-gray-300 ml-1">{{ $event->minuto }}'</span>
    </div>
@endif