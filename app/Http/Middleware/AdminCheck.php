<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminCheck
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('is_admin')) {
            // Si no está en sesión, redirigir al home
            return redirect('/')->with('error', 'Acceso denegado. Por favor, inicie sesión como administrador.');
        }

        return $next($request);
    }
}
