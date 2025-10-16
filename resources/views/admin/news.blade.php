<div id="news-content" class="admin-content space-y-6">
    
    {{-- FORMULARIO DE REGISTRO --}}
    <div class="card p-6">
        <h4 class="text-2xl font-semibold mb-4 text-green-400">Crear Nueva Noticia</h4>
        <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label for="news-title" class="block text-sm font-medium text-gray-400">Título</label>
                <input type="text" name="titulo" id="news-title" required value="{{ old('titulo') }}"
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
            </div>
            
            <div class="mb-4">
                <label for="news-content" class="block text-sm font-medium text-gray-400">Contenido</label>
                <textarea name="contenido" id="news-content" rows="4" required
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">{{ old('contenido') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="news-image" class="block text-sm font-medium text-gray-400">Imagen de Banner (Opcional)</label>
                <input type="file" name="imagen" id="news-image" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            </div>

            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                Publicar Noticia
            </button>
        </form>
    </div>

    {{-- LISTADO DE NOTICIAS CON PAGINACIÓN --}}
    <div class="card p-4 hover:transform-none">
        <h4 class="text-xl font-semibold mb-3">Noticias Publicadas (Página {{ $news->currentPage() }})</h4>
        
        <div class="space-y-3">
            @forelse($news as $article)
                <li class="p-3 bg-gray-700 rounded-md flex justify-between items-center flex-wrap">
                    
                    <div class="flex items-center space-x-3 w-full md:w-2/3">
                        @if($article->imagen_url)
                            <img src="{{ $article->imagen_url }}" class="w-12 h-12 rounded object-cover flex-shrink-0">
                        @endif
                        <div class="flex flex-col">
                            <span class="font-medium text-white">{{ $article->titulo }}</span>
                            <span class="text-xs text-gray-400">Publicado: {{ \Carbon\Carbon::parse($article->publicada_en)->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    {{-- ACCIONES --}}
                    <div class="flex space-x-2 mt-2 md:mt-0 w-full md:w-1/3 justify-end">
                        <a href="{{ route('news.show', $article->id) }}" target="_blank"
                            class="bg-gray-600 hover:bg-gray-500 text-white px-2 py-1 rounded-md text-xs">Ver</a>
                        
                        <a href="{{ route('admin.news.edit', $article->id) }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded-md text-xs">Editar</a>
                        
                        <form method="POST" action="{{ route('news.destroy', $article->id) }}"
                            onsubmit="return confirm('¿Seguro que quieres eliminar la noticia: {{ $article->titulo }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-md text-xs">Eliminar</button>
                        </form>
                    </div>
                </li>
            @empty
                <li class="p-2 text-center text-gray-500">No hay noticias publicadas.</li>
            @endforelse
        </div>
        
        {{-- Paginación --}}
        <div class="mt-4">
            {{ $news->links('pagination::tailwind') }}
        </div>
    </div>
</div>