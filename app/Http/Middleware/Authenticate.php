<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function registered(Request $request, $user)
{
    if ($user->role === 'entrenador') {
        return redirect('/trainer/calendar'); // Calendario para entrenadores
    } elseif ($user->role === 'alumno') {
        return redirect('/mapa'); // Mapa para alumnos
    }

    // Redirección por defecto
    return redirect('/home');
}
}
