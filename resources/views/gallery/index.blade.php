@extends('index') 

@section('content')
    <div class="max-w-7xl mx-auto py-8 w-full">
        {{-- MEJORA: Título con gradiente --}}
        <h2 class="text-3xl font-bold text-white mb-6 border-b border-secondary pb-2 flex items-center bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
            <span class="material-symbols-outlined mr-3 text-3xl text-secondary">photo_camera</span>
            Galería Oficial de la Continental League
        </h2>
        
        <p class="text-gray-400 mb-8">Revive los mejores momentos, goles y celebraciones de cada jornada.</p>

        {{-- ========================================== --}}
        {{-- 1. FORMULARIO DE SUBIDA PARA ADMIN (COMPLETO) --}}
        {{-- ========================================== --}}
        @if (session('is_admin'))
            <div class="bg-card-bg/80 backdrop-blur-lg border border-white/10 rounded-lg shadow-2xl p-6 mb-8">
                <h3 class="text-2xl font-bold mb-4 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                    <span class="material-symbols-outlined mr-2 align-middle">upload_file</span>
                    Subir Nueva Imagen a la Galería
                </h3>
                
                {{-- Mostramos errores de validación generales --}}
                @if ($errors->any())
                    <div class="bg-red-900/50 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-4 text-sm">
                        <strong class="font-bold">¡Error de validación!</strong>
                        <ul class="list-disc list-inside mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('gallery.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    {{-- Campo de Título --}}
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-300">Título (Opcional)</label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" 
                               class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:border-primary focus:ring-primary">
                        @error('titulo')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Campo de Imagen --}}
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-300">Archivo de Imagen (Max: 5MB)</label>
                        <input type="file" name="image" id="image" required 
                               class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90 file:cursor-pointer transition-all">
                        @error('image')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Botón de Subida --}}
                    <div>
                        <button type="submit" class="w-full justify-center flex items-center gap-3 px-4 py-3 rounded-lg text-white transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-glow bg-gradient-to-r from-primary to-emerald-600 hover:from-primary/90 hover:to-emerald-600/90">
                            <span class="material-symbols-outlined">add_photo_alternate</span>
                            Subir Imagen
                        </button>
                    </div>
                </form>
            </div>
        @endif
        {{-- ========================================== --}}
        {{-- FIN DEL FORMULARIO DE SUBIDA --}}
        {{-- ========================================== --}}


        {{-- ⬇️ GRID ASIMÉTRICO (Masonry Simulado) ⬇️ --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            
            @forelse($galleryItems as $item)
                @php
                    $rank = $loop->iteration;
                    $matchName = $item->match ? $item->match->localTeam?->nombre . ' vs ' . $item->match->visitorTeam?->nombre : 'Evento General';
                    $caption = $item->titulo ?: 'Foto de ' . $matchName;
                    
                    // Descripción para el lightbox (permite HTML)
                    $description = $matchName . '<br>' . $item->created_at->format('d/m/Y');

                    $gridClass = match ($rank) {
                        1 => 'col-span-2 row-span-2 aspect-square',
                        2 => 'col-span-2 md:col-span-1 aspect-video',
                        3 => 'col-span-1 aspect-square',
                        4 => 'col-span-1 aspect-video', 
                        default => 'col-span-1 aspect-square'
                    };
                @endphp
                
                {{-- CARD DE IMAGEN --}}
                <div 
                    class="group relative overflow-hidden shadow-xl transition duration-300 
                           {{ $gridClass }} rounded-lg border border-white/10 hover:border-primary/50 hover:shadow-glow">
                    
                    {{-- ⬇️ 1. BOTÓN DE ELIMINAR (SOLO ADMINS) ⬇️ --}}
                    @if (session('is_admin'))
                        <button 
                            data-url="{{ route('gallery.destroy', $item->id) }}"
                            {{-- 
                                ✅ DOBLE CORRECCIÓN:
                                1. event.stopPropagation(): Evita que el clic "atraviese" al enlace <a>.
                                2. event.preventDefault(): Evita que el enlace <a> se active.
                            --}}
                            onclick="event.preventDefault(); event.stopPropagation(); openDeleteModal(this)"
                            class="absolute top-2 right-2 z-20 p-2 bg-red-600/80 backdrop-blur-sm rounded-full text-white hover:bg-red-500 transition-all duration-200 transform hover:scale-110">
                            <span class="material-symbols-outlined" style="font-size: 1.25rem;">delete</span>
                        </button>
                    @endif
                    
                    {{-- ⬇️ 2. IMAGEN (AHORA ES UN ENLACE PARA GLIGHTBOX) ⬇️ --}}
                    <a href="{{ $item->image_url }}"
                       class="glightbox cursor-pointer w-full h-full"
                       data-gallery="continental-league"
                       data-title="{{ $caption }}"
                       data-description="{{ $description }}">
                        
                        <img src="{{ $item->image_url }}" 
                             alt="{{ $item->titulo }}" 
                             class="w-full h-full object-cover 
                                    transform group-hover:scale-110 transition duration-500 ease-in-out"
                        />
                        
                        {{-- OVERLAY DE INFORMACIÓN (Aparece en Hover) - (Se mantiene igual) --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4">
                            
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
                    </a>

                </div>
            @empty
                {{-- (Tu código de estado vacío se mantiene igual) --}}
                <div class="col-span-full bg-card-bg/80 backdrop-blur-lg border border-white/10 rounded-lg p-8 text-center shadow-xl">
                    <h4 class="text-xl text-white/80">¡La galería está vacía! El administrador subirá fotos pronto.</h4>
                </div>
            @endforelse
        </div>
        
        {{-- Paginación (Ya tiene el estilo "premium" desde index.html) --}}
        <div class="mt-8">
            {{ $galleryItems->links('pagination::tailwind') }} 
        </div>
    </div>
    {{-- ⬇️ MODAL DE CONFIRMACIÓN DE BORRADO ⬇️ --}}
    <div id="delete-modal" 
         class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        
        {{-- Tarjeta con efecto glow --}}
        <div class="bg-gray-800 card p-6 w-11/12 max-w-sm shadow-2xl hover:transform-none modal-card-glow">
            
            <h4 class="text-xl font-bold mb-2 text-red-400">¿Estás seguro?</h4>
            <p class="text-gray-300 mb-6">Esta acción no se puede deshacer.</p>
            
            {{-- Formulario que hará el borrado --}}
            <form id="delete-form" action="" method="POST">
                @csrf
                @method('DELETE')
                
                <div class="flex gap-4">
                    {{-- Botón de Cancelar (usa el nuevo JS) --}}
                    <button type="button" onclick="closeDeleteModal()" 
                            class="bg-gray-700 hover:bg-gray-600 text-gray-300 font-bold py-2 px-4 rounded-lg transition w-full">
                        Cancelar
                    </button>
                    
                    {{-- Botón de Confirmar (envía el formulario) --}}
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-500 text-white font-bold py-2 px-4 rounded-lg transition w-full">
                        Sí, Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection