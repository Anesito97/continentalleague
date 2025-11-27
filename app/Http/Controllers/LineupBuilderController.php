<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;

class LineupBuilderController extends Controller
{
    public function index()
    {
        $teams = Equipo::orderBy('nombre')->get();
        return view('lineup.index', compact('teams'));
    }

    public function getPlayers(Equipo $team)
    {
        $players = $team->jugadores()
            ->orderBy('posicion_general') // Sort by position roughly (GK, DEF, MID, FWD usually works if enum)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'posicion_general', 'numero', 'foto_url']);

        return response()->json($players);
    }

    public function proxyImage(Request $request)
    {
        $url = $request->query('url');

        if (!$url) {
            return response()->json(['error' => 'URL required'], 400);
        }

        try {
            $content = file_get_contents($url);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($content);

            return response($content)
                ->header('Content-Type', $mime)
                ->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Image not found'], 404);
        }
    }
}
