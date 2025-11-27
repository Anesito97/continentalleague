@props(['player', 'position'])

<a href="{{ route('player.profile', $player->id) }}"
    class="group flex flex-col items-center transform transition hover:scale-110 duration-200 cursor-pointer relative">
    
    <div class="relative mb-1">
        
        <div class="w-14 h-14 md:w-20 md:h-20 rounded-full border-[3px] border-white shadow-lg overflow-hidden bg-gray-800 group-hover:border-yellow-400 transition-colors relative z-10">
            @if(isset($player->foto_url) && $player->foto_url)
                <img src="{{ $player->foto_url }}" alt="{{ $player->nombre }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-sm md:text-xl font-bold text-gray-400">
                    {{ substr($player->nombre, 0, 1) }}
                </div>
            @endif
        </div>

        <div class="absolute -bottom-1 -right-1 md:bottom-0 md:right-0 bg-yellow-500 text-gray-900 border border-gray-900 rounded-full min-w-[20px] h-5 md:min-w-[24px] md:h-6 flex items-center justify-center z-20 shadow-md px-1">
            <span class="text-[10px] md:text-xs font-black leading-none">
                {{ number_format($player->rating ?? 0, 1) }}
            </span>
        </div>
    </div>

    <div class="flex flex-col items-center">
        <div class="bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded-full border border-white/10 group-hover:bg-black/80 transition-colors z-20">
            <p class="text-[10px] md:text-xs font-bold text-white whitespace-nowrap truncate max-w-[70px] md:max-w-[100px] text-center leading-tight">
                {{ $player->nombre }}
            </p>
        </div>
        
        <p class="text-[9px] md:text-[10px] text-gray-200 mt-0.5 drop-shadow-[0_1.2px_1.2px_rgba(0,0,0,0.8)] font-medium text-center truncate max-w-[80px]">
            {{ $player->equipo->nombre ?? 'Sin Equipo' }}
        </p>
    </div>
</a>