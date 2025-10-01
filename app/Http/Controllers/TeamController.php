<?php
namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TeamController extends Controller
{
    // Creado para la acción POST del formulario de creación
    public function store(Request $request)
    {
        // ... (validación)
        $logoUrl = 'https://placehold.co/50x50/1f2937/FFFFFF?text=LOGO';

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $file = $request->file('logo');
            $directory = public_path('uploads/logos');
            $filename = time() . '_' . $file->getClientOriginalName();

            // ⬇️ CREAR EL DIRECTORIO SI NO EXISTE ⬇️
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file->move($directory, $filename);
            $logoUrl = asset('uploads/logos/' . $filename);
        }

        // ... (creación de Equipo)
        Equipo::create([
            'nombre' => $request->name,
            'logros_descripcion' => $request->achievements ?? null,
            'escudo_url' => $logoUrl,
            // ...
        ]);

        return redirect()->route('admin.teams')->with('success', 'Equipo registrado con éxito.');
    }

    public function update(Request $request, Equipo $equipo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:equipos,nombre,' . $equipo->id,
            // No validamos 'logo' si no se sube
        ]);

        $data = ['nombre' => $request->nombre];

        // Manejar la nueva subida de logo
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $file = $request->file('logo');
            $directory = public_path('uploads/logos');
            $filename = time() . '_' . $file->getClientOriginalName();

            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file->move($directory, $filename);
            $data['escudo_url'] = asset('uploads/logos/' . $filename);

            // Opcional: Eliminar logo antiguo si no es el placeholder
            if ($equipo->escudo_url && strpos($equipo->escudo_url, 'placehold.co') === false) {
                // Lógica para intentar eliminar el archivo antiguo si es local
                // Por ejemplo: File::delete(public_path(str_replace(asset(''), '', $equipo->escudo_url)));
            }
        }

        $equipo->update($data);

        return redirect()->route('admin.teams')->with('success', 'Equipo actualizado con éxito.');
    }

    public function destroy(Equipo $equipo)
    {
        // Opcional: Eliminar el archivo asociado si existe (como en update)
        $equipo->delete();

        return redirect()->route('admin.teams')->with('success', 'Equipo eliminado.');
    }
}