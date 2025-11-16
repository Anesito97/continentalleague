@extends('index') 

@section('content')
    <div class="max-w-7xl mx-auto py-8 w-full">
        {{-- MEJORA: Título con gradiente --}}
        <h2 class="text-3xl font-bold text-white mb-6 border-b border-primary pb-2 flex items-center bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
            <span class="material-symbols-outlined mr-3 text-3xl text-primary">newspaper</span>
            Noticias de la Liga
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($news as $article)
                {{-- MEJORA: "Glassmorphism" para la tarjeta y efecto "group-hover" --}}
                <div class="group bg-card-bg/80 backdrop-blur-lg border border-white/10 rounded-lg shadow-xl hover:border-primary/50 transition duration-300 flex flex-col overflow-hidden">
                    
                    {{-- IMAGEN --}}
                    <div class="h-40 overflow-hidden">
                        <img src="{{ $article->imagen_url ?? asset('images/default_news.jpg') }}" 
                             alt="{{ $article->titulo }}" 
                             {{-- MEJORA: Zoom en hover de la tarjeta --}}
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                    </div>

                    {{-- CONTENIDO --}}
                    <div class="p-4 flex flex-col flex-grow">
                        <span class="text-xs font-semibold text-primary mb-1">
                            {{ \Carbon\Carbon::parse($article->publicada_en)->format('d/m/Y') }}
                        </span>
                        
                        <h4 class="text-xl font-bold text-white mb-3 leading-snug flex-grow">
                            {{ $article->titulo }}
                        </h4>
                        
                        <a href="{{ route('news.show', $article->id) }}" class="mt-4 text-sm font-semibold text-primary hover:text-green-400 transition flex items-center self-start">
                            Leer más <span class="material-symbols-outlined ml-1 text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            @empty
                {{-- MEJORA: "Glassmorphism" para el estado vacío --}}
                <div class="md:col-span-3 bg-card-bg/80 backdrop-blur-lg border border-white/10 rounded-lg p-8 text-center shadow-xl">
                    <h4 class="text-xl text-white/80">No hay noticias publicadas actualmente.</h4>
                </div>
            @endforelse
        </div>
        
        {{-- Paginación de Laravel (Ahora con estilos "premium" desde index.html) --}}
        <div class="mt-8">
            {{ $news->links('pagination::tailwind') }} 
        </div>
    </div>
@endsection