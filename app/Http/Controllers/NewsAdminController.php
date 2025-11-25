<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use App\Traits\LoadsCommonData;

class NewsAdminController extends Controller
{
    use LoadsCommonData;

    public function adminNews()
    {
        // Cargar las noticias paginadas reales
        $news = \App\Models\Noticia::orderBy('publicada_en', 'desc')->paginate(10);

        session(['activeAdminContent' => 'news']);
        $data = $this->loadAllData();

        // Aquí, $data['news'] obtiene los datos reales, NO el paginador vacío.
        $data['news'] = $news;
        $data['activeView'] = 'admin';

        return view('index', $data);
    }

    public function editNews(Noticia $noticia)
    {
        // Cargar todos los datos base (equipos, jugadores) necesarios para el layout
        $data = $this->loadAllData();

        // Asignar el ítem actual y su tipo para la vista edit.blade.php
        $data['item'] = $noticia;
        $data['type'] = 'news';

        // Necesitas pasar los equipos si el formulario de edición (edit.blade.php) los requiere
        // $data['teams'] = $data['teams'];

        return view('edit', $data);
    }
    // Creado para la acción POST del formulario de creación
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageUrl = null;

        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
            $file = $request->file('imagen');
            $directory = public_path('uploads/news_banners');
            $filename = time() . '_' . $file->getClientOriginalName();

            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file->move($directory, $filename);
            $imageUrl = asset('uploads/news_banners/' . $filename);
        }

        Noticia::create([
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'imagen_url' => $imageUrl,
            'publicada_en' => now(),
        ]);

        return redirect()->route('admin.news')->with('success', 'Noticia registrada con éxito.');
    }

    public function destroy(Noticia $noticia)
    {
        $noticia->delete();

        return redirect()->route('admin.news')->with('success', 'Noticia eliminada.');
    }

    public function update(Request $request, Noticia $noticia)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación para nueva imagen
        ]);

        $data = $request->only(['titulo', 'contenido']);
        $data['publicada_en'] = $request->publicada_en ?? now(); // Mantener fecha o actualizar

        // Manejo de la subida de la nueva imagen
        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {

            // Opcional: Eliminar la imagen antigua si existe
            // ... (Lógica para borrar archivo) ...

            $file = $request->file('imagen');
            $directory = public_path('uploads/news_banners');
            $filename = time() . '_' . $file->getClientOriginalName();

            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file->move($directory, $filename);
            $data['imagen_url'] = asset('uploads/news_banners/' . $filename);
        }

        $noticia->update($data);

        return redirect()->route('admin.news')->with('success', 'Noticia actualizada con éxito.');
    }
}
