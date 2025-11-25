<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class VoteController extends Controller
{
    private const COOKIE_LIFETIME_MINUTES = 60 * 72;

    private const CACHE_KEY_PREFIX = 'match_votes_';

    public function handleVote(Request $request, $match_id)
    {
        $voto = $request->input('voto');
        $cookieName = 'voted_'.$match_id;

        // 1. VERIFICACIÓN DE LA COOKIE (Evita votos múltiples por usuario)
        if ($request->cookie($cookieName)) {
            return back()->with('error', 'Ya has votado en este partido.');
        }

        // 2. ACTUALIZAR CONTEO GLOBAL EN CACHE (Esta lógica se mantiene)
        $cacheKey = self::CACHE_KEY_PREFIX.$match_id;
        $votes = Cache::get($cacheKey, ['local' => 0, 'draw' => 0, 'visitor' => 0]);

        if (in_array($voto, ['local', 'draw', 'visitor'])) {
            $votes[$voto]++;
            Cache::put($cacheKey, $votes, 60 * 24 * 30);
        }

        // 3. RESPUESTA: Configurar la Cookie y Redirigir

        // Creamos la instancia de la cookie
        $cookie = Cookie::make($cookieName, true, self::COOKIE_LIFETIME_MINUTES);

        // ⬇️ FIX: Usar el helper redirect()->back() y encadenar el método cookie() ⬇️
        return redirect()->back()
            ->withCookie($cookie) // Adjunta la cookie a la respuesta de redirección
            ->with('success', '¡Gracias por tu voto!');
    }

    /**
     * Función auxiliar para obtener el conteo de votos (necesario para el DashboardController).
     */
    public static function getVotes($match_id)
    {
        $cacheKey = self::CACHE_KEY_PREFIX.$match_id;

        return Cache::get($cacheKey, ['local' => 0, 'draw' => 0, 'visitor' => 0]);
    }
}
