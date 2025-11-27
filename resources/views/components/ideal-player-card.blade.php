@props(['player', 'position'])
@php
    // Map full position names to short codes
    $posCode = match (strtolower($position)) {
        'delantero' => 'DEL',
        'medio' => 'MED',
        'defensa' => 'DEF',
        'portero' => 'POR',
        default => substr($position, 0, 3)
    };
@endphp

<a href="{{ route('player.profile', $player->id) }}"
    class="group relative flex flex-col items-center transform transition hover:scale-110 duration-200 cursor-pointer z-10">

    <!-- FIFA Card Container -->
    <div class="relative w-16 h-24 md:w-24 md:h-36 rounded-t-lg rounded-b-md shadow-2xl overflow-hidden border-2 border-[#fceda3]"
         style="background: radial-gradient(circle at 50% 0%, #ffffff 0%, #fceda3 25%, #dfb658 60%, #a67c2e 100%);">
        
        <!-- Shine Effect -->
        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/40 to-transparent opacity-70 pointer-events-none"></div>

        <!-- Inner Border/Pattern -->
        <div class="absolute inset-1 border border-[#a67c2e]/50 rounded-t-md rounded-b-sm"></div>

        <!-- Top Info (Rating & Position) -->
        <div class="absolute top-1 left-1 md:top-2 md:left-2 flex flex-col leading-none z-20">
            <span class="text-lg md:text-2xl font-black text-white drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)]">{{ number_format($player->rating, 0) }}</span>
            <span class="text-[8px] md:text-[10px] font-bold text-white uppercase tracking-wide drop-shadow-[0_1px_1px_rgba(0,0,0,0.8)]">{{ $posCode }}</span>
        </div>

        <!-- Player Image -->
        <div class="absolute top-4 md:top-6 left-1/2 transform -translate-x-1/2 w-12 h-12 md:w-20 md:h-20 z-10">
            @if($player->foto_url)
                <img src="{{ $player->foto_url }}" alt="{{ $player->nombre }}"
                    class="w-full h-full object-cover object-top drop-shadow-lg mask-image-gradient">
            @else
                <div class="w-full h-full flex items-center justify-center text-xs md:text-lg font-bold text-[#8a681c]">
                    {{ substr($player->nombre, 0, 1) }}
                </div>
            @endif
        </div>

        <!-- Bottom Info (Name & Team) -->
        <div class="absolute bottom-0 w-full pt-4 pb-1 px-1 flex flex-col items-center z-20">
            <!-- Gradient Overlay for Text Readability -->
            <div class="absolute bottom-0 inset-x-0 h-1/2 bg-gradient-to-t from-[#5e4312] to-transparent opacity-80 -z-10"></div>

            <!-- Name -->
            <p class="text-[8px] md:text-[10px] font-black text-white uppercase tracking-tighter truncate w-full text-center leading-tight drop-shadow-[0_1px_1px_rgba(0,0,0,0.8)]">
                {{ $player->nombre }}
            </p>
            
            <!-- Divider -->
            <div class="w-3/4 h-px bg-white/50 my-0.5"></div>

            <!-- Team Logo (Small) -->
             <div class="flex items-center justify-center gap-1">
                @if($player->equipo && $player->equipo->escudo_url)
                    <img src="{{ $player->equipo->escudo_url }}" class="w-2 h-2 md:w-3 md:h-3 object-contain drop-shadow-sm" alt="Team">
                @endif
                <span class="text-[6px] md:text-[8px] font-bold text-white truncate max-w-[40px] md:max-w-[60px] drop-shadow-sm">
                    {{ $player->equipo->nombre ?? 'FA' }}
                </span>
            </div>
        </div>
    </div>
</a>