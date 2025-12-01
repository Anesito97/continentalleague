<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\Http\Request; // Asegúrate de que este sea el modelo correcto
use Illuminate\Support\Facades\File;

class GalleryController extends Controller
{
    /**
     * Muestra la página principal de la galería.
     * (Este es el método que soluciona el 404)
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtenemos los ítems de la galería, ordenados por más nuevo
        // y paginados (como espera tu vista)
        $galleryItems = GalleryItem::orderBy('created_at', 'desc')->paginate(15); // O el número que prefieras

        // Retornamos la vista 'gallery' (o como se llame tu vista blade)
        // y le pasamos los datos que necesita.
        return view('gallery.index', ['galleryItems' => $galleryItems]);
    }

    /**
     * Almacena una nueva imagen en la galería.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upload(Request $request, \App\Services\WhatsAppService $whatsapp)
    {
        // 1. Validación de la solicitud (se mantiene igual)
        $request->validate([
            'titulo' => 'nullable|string|max:255',
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp,gif',
                'max:5120', // 5MB
            ],
        ], [
            'image.required' => 'Debes seleccionar un archivo de imagen.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, webp o gif.',
            'image.max' => 'La imagen no puede pesar más de 5MB.',
        ]);

        $url = null;
        $physicalPath = null;

        // 2. Almacenar la imagen (SIGUIENDO TU EJEMPLO)
        if ($request->hasFile('image') && $request->file('image')->isValid()) { // <-- 2. Añadido isValid()

            $file = $request->file('image');

            // Definimos la ruta de destino (la carpeta public/gallery)
            $directory = public_path('gallery');

            // Generamos un nombre de archivo único
            $filename = uniqid('gallery_') . '.' . $file->getClientOriginalExtension();

            try {
                // 3. Comprobar y crear el directorio si no existe
                if (!File::isDirectory($directory)) {
                    File::makeDirectory($directory, 0777, true, true);
                }

                // 4. Movemos el archivo directamente a public/gallery
                $file->move($directory, $filename);

                // 5. Esta es la URL pública COMPLETA que guardaremos
                $url = asset('gallery/' . $filename);

                // Guardamos la ruta física completa por si falla la BD
                $physicalPath = $directory . '/' . $filename;

            } catch (\Exception $e) {
                // Manejar error de movimiento de archivo
                return back()->with('error', 'Error al guardar la imagen: ' . $e->getMessage());
            }
        }

        if (!$url) {
            return back()->with('error', 'No se pudo generar la URL de la imagen.');
        }

        // 3. Crear el registro en la base de datos
        try {
            $item = GalleryItem::create([
                'titulo' => $request->input('titulo'),
                'image_url' => $url, // <-- 6. Guardamos la URL completa (ej: http://localhost/gallery/...)
                'partido_id' => $request->input('match_id'),
                'uploaded_by_user_id' => auth()->id(),
            ]);

            // --- NOTIFICACIÓN WHATSAPP ---
            try {
                $galleryUrl = route('gallery.index');
                $title = $request->input('titulo') ? "*{$request->input('titulo')}*" : "";

                $message = "📸 *NUEVA FOTO EN LA GALERÍA* 📸\n\n" .
                    "{$title}\n" .
                    "Se ha subido una nueva imagen a la galería.\n\n" .
                    "🔗 Ver galería: {$galleryUrl}";

                $whatsapp->sendMessage($message);

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error enviando WhatsApp (Galería): " . $e->getMessage());
            }

        } catch (\Exception $e) {
            // En caso de error de BD, eliminamos el archivo subido
            if ($physicalPath && file_exists($physicalPath)) {
                unlink($physicalPath);
            }

            return back()->with('error', 'Error al guardar en la base de datos: ' . $e->getMessage());
        }

        // 4. Redirigir con éxito
        return back()->with('success', '¡Imagen subida con éxito!');
    }

    public function destroy(GalleryItem $item)
    {
        try {
            // 1. Obtener la ruta física del archivo
            // Asumiendo que guardaste la URL completa (http://...)
            $urlPath = parse_url($item->image_url, PHP_URL_PATH); // Extrae "/gallery/imagen.jpg"
            $physicalPath = public_path($urlPath); // Convierte a "C:/.../public/gallery/imagen.jpg"

            // 2. Eliminar el archivo físico si existe
            if (File::exists($physicalPath)) {
                File::delete($physicalPath);
            }

            // 3. Eliminar el registro de la base de datos
            $item->delete();

            return back()->with('success', 'Imagen eliminada con éxito.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la imagen: ' . $e->getMessage());
        }
    }
}
