<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use App\Traits\LoadsCommonData;

class PlayerController extends Controller
{
    use LoadsCommonData;

    public function adminPlayers()
    {
        session(['activeAdminContent' => 'players']);
        $data = $this->loadAllData();
        $data['positions'] = $this->getPositions();
        $data['news'] = $this->getEmptyNewsPaginator();
        $data['activeView'] = 'admin';

        return view('index', $data);
    }

    public function editPlayer(Jugador $jugador)
    {
        $data = $this->loadAllData();
        $data['item'] = $jugador;
        $data['positions'] = $this->getPositions();
        $data['type'] = 'player';

        return view('edit', $data);
    }
    // Creado para la acción POST del formulario de creación
    public function store(Request $request)
    {
        // ... (validación)
        $photoUrl = asset('player.png');

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $file = $request->file('logo');
            $directory = public_path('uploads/player_photos');
            $filename = time() . '_' . $file->getClientOriginalName();

            // ⬇️ CREAR EL DIRECTORIO SI NO EXISTE ⬇️
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file->move($directory, $filename);
            $logoUrl = asset('uploads/player_photos/' . $filename);
        }

        Jugador::create([
            'nombre' => $request->name,
            'numero' => $request->number,
            'equipo_id' => $request->teamId,
            'posicion_general' => $request->posicion_general,
            'posicion_especifica' => $request->posicion_especifica,
            'posicion' => $request->position,
            'foto_url' => $photoUrl,
        ]);

        return redirect()->route('admin.players')->with('success', 'Jugador registrado con éxito.');
    }

    public function update(Request $request, Jugador $jugador)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'numero' => 'required|integer|min:1|max:99',
            'equipo_id' => 'required|exists:equipos,id',
            'posicion_general' => 'required|string',
            'posicion_especifica' => 'required|string',
            'esta_lesionado' => 'nullable|boolean',
        ]);

        $data = $request->only(['nombre', 'numero', 'equipo_id', 'posicion_general', 'posicion_especifica']);

        $data['esta_lesionado'] = $request->has('esta_lesionado');

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file = $request->file('photo');
            $directory = public_path('uploads/player_photos');
            $filename = time() . '_' . $file->getClientOriginalName();

            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file->move($directory, $filename);
            $data['foto_url'] = asset('uploads/player_photos/' . $filename);
        }

        $jugador->update($data);

        return redirect()->route('admin.players')->with('success', 'Jugador actualizado con éxito.');
    }

    public function destroy(Jugador $jugador)
    {
        $jugador->delete();

        return redirect()->route('admin.players')->with('success', 'Jugador eliminado.');
    }
}
