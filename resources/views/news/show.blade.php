@extends('index')

@section('content')
    <div class="max-w-4xl mx-auto py-8 w-full">
        
        <a href="{{ route('news.index') }}" class="text-sm text-primary hover:text-green-400 mb-6 inline-flex items-center">
            <span class="material-symbols-outlined mr-1">arrow_back</span> Volver a Noticias
        </a>

        <div class="card p-6 shadow-2xl">
            
            <h1 class="text-4xl font-extrabold text-white mb-4 leading-tight">
                {{ $noticia->titulo }}
            </h1>
            
            <p class="text-sm text-gray-400 mb-6 border-b border-gray-700 pb-3">
                Publicado el {{ \Carbon\Carbon::parse($noticia->publicada_en)->format('d/m/Y H:i') }}
            </p>
            
            {{-- IMAGEN DESTACADA --}}
            @if($noticia->imagen_url)
                <img src="{{ $noticia->imagen_url }}" 
                     alt="{{ $noticia->titulo }}" 
                     class="w-full max-h-96 object-cover rounded-lg mb-6 shadow-lg">
            @endif

            {{-- CONTENIDO COMPLETO --}}
            <div class="text-lg text-gray-300 space-y-4 leading-relaxed">
                <p>{{ $noticia->contenido }}</p>
                {{-- Si el contenido es HTML, usa {!! $noticia->contenido !!} en lugar de {{ $noticia->contenido }} --}}
            </div>
            
        </div>
    </div>
@endsection