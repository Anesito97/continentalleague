<div class="space-y-4">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-400">Título</label>
        <input type="text" name="titulo" value="{{ old('titulo', $item->titulo) }}" required 
            class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
    </div>
    
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-400">Contenido</label>
        <textarea name="contenido" rows="6" required 
            class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">{{ old('contenido', $item->contenido) }}</textarea>
    </div>
    
    <div class="border-t border-gray-600 pt-3">
        <label class="block text-sm font-medium text-gray-400">Imagen Actual</label>
        @if($item->imagen_url)
            <img src="{{ $item->imagen_url }}" class="w-32 h-20 object-cover my-2 rounded">
        @else
            <p class="text-xs text-gray-500 my-2">No hay imagen subida.</p>
        @endif
        <label class="block text-sm font-medium text-gray-400">Subir Nueva Imagen</label>
        <input type="file" name="imagen" accept="image/*" class="w-full text-sm text-gray-300">
    </div>
    
    <input type="hidden" name="publicada_en" value="{{ $item->publicada_en }}">
</div>