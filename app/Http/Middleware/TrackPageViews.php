<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageViews
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo rastrear peticiones GET
        if (!$request->isMethod('get')) {
            return $next($request);
        }

        // Excluir rutas de administración, API, debug, assets, etc.
        if (
            $request->is('admin/*') ||
            $request->is('api/*') ||
            $request->is('_debugbar/*') ||
            $request->is('sanctum/*') ||
            $request->ajax()
        ) {
            return $next($request);
        }

        try {
            \App\Models\PageView::create([
                'url' => $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silenciar errores de tracking para no afectar al usuario
            \Illuminate\Support\Facades\Log::error('Error tracking page view: ' . $e->getMessage());
        }

        return $next($request);
    }
}
