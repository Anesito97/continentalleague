@extends('index') 

@section('content')
    <div class="max-w-7xl mx-auto py-8 w-full">
        <h2 class="text-3xl font-bold text-white mb-6 border-b border-primary pb-2 flex items-center">
            <span class="material-symbols-outlined mr-3 text-3xl text-primary">newspaper</span>
            Noticias de la Liga
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($news as $article)
                <div class="card overflow-hidden shadow-xl hover:scale-[1.01] transition duration-300 flex flex-col">
                    
                    {{-- IMAGEN --}}
                    <div class="h-40 overflow-hidden">
                        <img src="{{ $article->imagen_url ?? asset('images/default_news.jpg') }}" 
                             alt="{{ $article->titulo }}" 
                             class="w-full h-full object-cover">
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
                <div class="md:col-span-3 card p-8 text-center bg-gray-800 shadow-xl">
                    <h4 class="text-xl text-white/80">No hay noticias publicadas actualmente.</h4>
                </div>
            @endforelse
        </div>
        
        {{-- Paginación de Laravel --}}
        <div class="mt-8">
            {{ $news->links('pagination::tailwind') }} 
        </div>
    </div>
@endsection