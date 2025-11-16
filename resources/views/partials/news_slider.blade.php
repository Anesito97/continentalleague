@php
    $newsItems = $newsItems->sortByDesc('publicada_en'); 
    $defaultImage = asset('uploads/news/default_banner.jpg'); 
@endphp

<section class="mb-8 p-4 sm:p-0">
    {{-- MEJORA: Título con gradiente --}}
    <h3 class="text-3xl font-bold mb-4 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
        Últimas Novedades
    </h3>

    @if ($newsItems->isNotEmpty())
        {{-- CONTENEDOR DEL SLIDER SWIPER (Ahora más simple y eficiente) --}}
        <div class="swiper news-swiper"> 

            <div class="swiper-wrapper">
                @foreach ($newsItems as $news)
                    <div class="swiper-slide card overflow-hidden shadow-2xl transition duration-300">
                        <div class="flex flex-col md:flex-row">

                            {{-- IMAGEN (Añadir clase para Zoom Effect) --}}
                            <div class="w-full md:w-1/2 h-64 md:h-80 overflow-hidden relative">
                                <img src="{{ $news->imagen_url ?? $defaultImage }}" alt="{{ $news->titulo }}"
                                    class="w-full h-full object-cover transition duration-700 ease-in-out hover:scale-110">
                                {{-- Gradiente Oscuro en Borde --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                            </div>

                            {{-- CONTENIDO --}}
                            {{-- MEJORA: "Glassmorphism" aplicado al contenido del slide --}}
                            <div class="w-full md:w-1/2 p-6 md:p-8 flex flex-col justify-center bg-card-bg/70 backdrop-blur-lg">
                                <span class="text-xs font-semibold text-primary mb-1 uppercase">
                                    Publicado: {{ \Carbon\Carbon::parse($news->publicada_en)->diffForHumans() }}
                                </span>
                                <h4 class="text-3xl font-extrabold text-white mb-3 leading-snug">
                                    {{ $news->titulo }}
                                </h4>
                                <p class="text-gray-300 mb-4 line-clamp-3">
                                    {{ Str::limit($news->contenido, 150) }}
                                </p>
                                <a href="{{ $news->titulo === '¡Reglamento Oficial Publicado! Conoce las Normas de Disciplina y Plazos Clave.' ? 'http://98.84.179.211/rules' : route('news.show', $news->id) }}"
                                    class="text-sm font-semibold text-primary hover:text-green-400 transition flex items-center">
                                    Leer más <span class="material-symbols-outlined ml-1 text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- NAVEGACIÓN (Flechas más pequeñas) --}}
            {{-- MEJORA: Flechas con "Glassmorphism" y hover --}}
            <div class_name="swiper-button-prev text-primary bg-gray-900/50 backdrop-blur-sm rounded-full w-10 h-10 after:text-sm hover:bg-primary hover:text-white transition-all"></div>
            <div class_name="swiper-button-next text-primary bg-gray-900/50 backdrop-blur-sm rounded-full w-10 h-10 after:text-sm hover:bg-primary hover:text-white transition-all"></div>

            {{-- PAGINACIÓN (Puntos) --}}
            {{-- Nota: Estilar los puntos requiere CSS en el <style> principal. --}}
            <div class="swiper-pagination mt-4"></div>

        </div> {{-- Fin del swiper --}}
    @else
        {{-- MENSAJE DE BIENVENIDA --}}
        {{-- MEJORA: "Glassmorphism" para la tarjeta de bienvenida --}}
        <div class="card p-8 text-center bg-card-bg/70 backdrop-blur-lg border border-white/10 border-b-4 border-primary/50 shadow-xl">
            <h4 class="text-4xl font-extrabold text-primary mb-2">¡Bienvenido a la Continental League!</h4>
            <p class="text-xl text-white/80">
                Aún no hay noticias destacadas. ¡Regresa pronto para ver el resumen de la jornada y las estadísticas!
            </AR>
        </div>
    @endif
</section>