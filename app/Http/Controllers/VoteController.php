<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class VoteController extends Controller
{
    private const COOKIE_LIFETIME_MINUTES = 60 * 72;

    private const CACHE_KEY_PREFIX = 'match_votes_';

    public function handleVote(Request $request, $match_id)
    {
        // 1. Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para votar.');
        }

        $request->validate([
            'voto' => 'required|in:local,draw,visitor',
        ]);

        $user = Auth::user();
        $voto = $request->input('voto');

        // 2. Verificar si ya votó en este partido (DB check)
        $existingVote = \App\Models\Vote::where('user_id', $user->id)
            ->where('match_id', $match_id)
            ->first();

        if ($existingVote) {
            // Opcional: Permitir cambiar el voto
            $existingVote->update(['vote' => $voto]);
            $message = 'Tu voto ha sido actualizado.';
        } else {
            // Crear nuevo voto
            \App\Models\Vote::create([
                'user_id' => $user->id,
                'match_id' => $match_id,
                'vote' => $voto,
            ]);
            $message = '¡Gracias por tu voto!';
        }

        return back()->with('success', $message);
    }

    /**
     * Función auxiliar para obtener el conteo de votos (necesario para el DashboardController).
     */
    public static function getVotes($match_id)
    {
        // Obtener conteo real de la base de datos
        $votes = \App\Models\Vote::where('match_id', $match_id)
            ->selectRaw('vote, count(*) as count')
            ->groupBy('vote')
            ->pluck('count', 'vote')
            ->toArray();

        return [
            'local' => $votes['local'] ?? 0,
            'draw' => $votes['draw'] ?? 0,
            'visitor' => $votes['visitor'] ?? 0,
        ];
    }
}
