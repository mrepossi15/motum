<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Verifica si el usuario tiene el rol requerido
        if (Auth::check() && Auth::user()->role === $role) {
            return $next($request);
        }

        // Si no tiene el rol requerido, redirige o muestra un error
        abort(403, 'No tienes acceso a esta sección.');
    }
}