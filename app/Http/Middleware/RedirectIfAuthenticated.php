<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
            
                // Redirigir según el rol
                if ($user->role === 'entrenador') {
                    return redirect('/trainer/calendar'); // Calendario para entrenadores
                } elseif ($user->role === 'alumno') {
                    return redirect('/mapa'); // Mapa para alumnos
                }
            
                // Redirección por defecto si no hay rol
                return redirect('/home');
            }
        }

        return $next($request);
    }
}
