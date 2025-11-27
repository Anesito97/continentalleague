@props(['player', 'position'])

<a href="{{ route('player.profile', $player->id) }}"
    class="group flex flex-col items-center transform transition hover:scale-110 duration-200 cursor-pointer z-10">
    <!-- Player Image -->
    <div
        class="relative w-12 h-12 md:w-16 md:h-16 rounded-full border-2 md:border-4 border-white shadow-lg overflow-hidden bg-gray-800 group-hover:border-yellow-400 transition-colors">
        @if($player->foto_url)
            <img src="{{ $player->foto_url }}" alt="{{ $player->nombre }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-sm md:text-lg font-bold text-gray-500">
                {{ substr($player->nombre, 0, 1) }}
            </div>
        @endif

        <!-- Rating Badge -->
        <div
            class="absolute bottom-0 right-0 bg-yellow-500 text-black text-[10px] md:text-xs font-bold px-1 py-0.5 rounded-full border border-white leading-none">
            {{ number_format($player->rating, 1) }}
        </div>
    </div>

    <!-- Player Info -->
    <div class="mt-1 text-center">
        <div
            class="bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded-full border border-white/10 group-hover:bg-black/80 transition-colors">
            <p
                class="text-[10px] md:text-xs font-bold text-white whitespace-nowrap truncate max-w-[80px] md:max-w-[100px]">
                {{ $player->nombre }}
            </p>
        </div>
        <p class="text-[9px] md:text-[10px] text-gray-300 mt-0.5 shadow-black drop-shadow-md font-medium">
            {{ $player->equipo->nombre ?? 'Sin Equipo' }}
        </p>
    </div>
</a>