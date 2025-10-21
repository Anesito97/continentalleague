@extends('index') 

@section('content')
    <div class="max-w-7xl mx-auto py-8 w-full">
        <h2 class="text-3xl font-bold text-white mb-6 border-b border-secondary pb-2 flex items-center">
            <span class="material-symbols-outlined mr-3 text-3xl text-secondary">photo_camera</span>
            Galería Oficial de la Continental League
        </h2>
        
        <p class="text-gray-400 mb-8">Revive los mejores momentos, goles y celebraciones de cada jornada.</p>

        {{-- ⬇️ GRID ASIMÉTRICO (Masonry Simulado) ⬇️ --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            
            @forelse($galleryItems as $item)
                @php
                    $rank = $loop->iteration;
                    $matchName = $item->match ? $item->match->localTeam?->nombre . ' vs ' . $item->match->visitorTeam?->nombre : 'Evento General';
                    $caption = $item->titulo ?: 'Foto de ' . $matchName;
                    
                    // LÓGICA ASIMÉTRICA: 
                    // 1. Imagen 1 (Rank 1): Grande y alta (col-span-2, row-span-2)
                    // 2. Imagen 2-3 (Rank 2-3): Tamaño medio (col-span-1, aspect-video para variar altura)
                    // 3. Imagen 4-5 (Rank 4+): Tamaño estándar
                    
                    $gridClass = match ($rank) {
                        1 => 'col-span-2 row-span-2 aspect-square', // Más grande y cuadrada
                        2 => 'col-span-2 md:col-span-1 aspect-video', // Larga, luego pequeña
                        3 => 'col-span-1 aspect-square', // Pequeña estándar
                        4 => 'col-span-1 aspect-video', 
                        default => 'col-span-1 aspect-square' // El resto son pequeñas y cuadradas
                    };
                @endphp
                
                {{-- CARD DE IMAGEN --}}
                <div 
                    class="group relative card overflow-hidden shadow-xl transition duration-300 
                           cursor-pointer {{ $gridClass }}" 
                    {{-- ⬇️ LLAMADA AL LIGHTBOX ⬇️ --}}
                    onclick="openLightbox('{{ $item->image_url }}', '{{ $caption }}')">
                    
                    {{-- IMAGEN: Efecto de Zoom Suave al Hover --}}
                    <img src="{{ $item->image_url }}" 
                         alt="{{ $item->titulo }}" 
                         class="w-full h-full object-cover 
                                transform group-hover:scale-110 transition duration-500 ease-in-out"
                    />

                    {{-- OVERLAY DE INFORMACIÓN (Aparece en Hover) --}}
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4">
                        
                        <h4 class="text-xl font-extrabold text-primary mb-1 line-clamp-2">
                            {{ $item->titulo ?: 'Foto de la Jornada' }}
                        </h4>
                        
                        <p class="text-xs text-white/80 font-semibold mb-1">
                            {{ $matchName }}
                        </p>
                        
                        <p class="text-xs text-gray-400">
                            {{ $item->created_at->format('d/m/Y') }}
                        </p>
                    </div>

                </div>
            @empty
                <div class="col-span-full card p-8 text-center bg-gray-800 shadow-xl">
                    <h4 class="text-xl text-white/80">¡La galería está vacía! El administrador subirá fotos pronto.</h4>
                </div>
            @endforelse
        </div>
        
        {{-- Paginación --}}
        <div class="mt-8">
            {{ $galleryItems->links('pagination::tailwind') }} 
        </div>
    </div>
@endsection