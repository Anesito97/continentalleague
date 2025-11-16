@extends('index')

@section('content')
    <div class="max-w-4xl mx-auto py-8 w-full">
        
        {{-- MEJORA: Botón "Pill" (píldora) con estética "glassmorphism" --}}
        <a href="{{ route('news.index') }}" class="text-sm text-primary mb-6 inline-flex items-center bg-gray-900/50 backdrop-blur-sm border border-white/10 rounded-full px-3 py-1 hover:border-primary/50 hover:text-green-400 transition-all">
            <span class="material-symbols-outlined mr-1">arrow_back</span> Volver a Noticias
        </a>

        {{-- MEJORA: "Glassmorphism" para la tarjeta principal --}}
        <div class="bg-card-bg/80 backdrop-blur-lg border border-white/10 rounded-lg shadow-2xl p-6">
            
            {{-- MEJORA: Título con gradiente --}}
            <h1 class="text-4xl font-extrabold mb-4 leading-tight bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                {{ $noticia->titulo }}
            </h1>
            
            {{-- MEJORA: Borde del color del tema --}}
            <p class="text-sm text-gray-400 mb-6 border-b border-white/10 pb-3">
                Publicado el {{ \Carbon\Carbon::parse($noticia->publicada_en)->format('d/m/Y H:i') }}
            </p>
            
            {{-- IMAGEN DESTACADA --}}
            @if($noticia->imagen_url)
                {{-- MEJORA: Sombra "glow" verde para la imagen --}}
                <img src="{{ $noticia->imagen_url }}" 
                     alt="{{ $noticia->titulo }}" 
                     class="w-full max-h-96 object-cover rounded-lg mb-6 shadow-lg shadow-primary/30">
            @endif

            {{-- CONTENIDO COMPLETO --}}
            <div class="text-lg text-gray-300 space-y-4 leading-relaxed">
                <p>{{ $noticia->contenido }}</p>
                {{-- Si el contenido es HTML, usa {!! $noticia->contenido !!} en lugar de {{ $noticia->contenido }} --}}
            </div>
            
        </div>
    </div>
@endsection