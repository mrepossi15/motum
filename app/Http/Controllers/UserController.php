<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Park;
use App\Models\UserExperience;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use App\Traits\HandlesImages;

class UserController extends Controller
{
    use HandlesImages;
    //////REGISTRO ALUMNO

     //////LOGIN & LOGOUT
    
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Verificar si el email existe en la base de datos
    $userExists = \App\Models\User::where('email', $request->email)->exists();

    if ($userExists) {
        // Intentar autenticar con las credenciales proporcionadas
        if (Auth::attempt($request->only('email', 'password'))) {
            // Redirigir según el rol del usuario autenticado
            $role = Auth::user()->role;

            if ($role === 'entrenador') {
                return redirect()->route('trainer.calendar');
            } elseif ($role === 'alumno') {
                return redirect()->route('map');
            }
        }

        // Si el email existe pero la contraseña es incorrecta
        return back()->withErrors(['password' => 'La contraseña es incorrecta'])
                     ->withInput($request->only('email')); // Retener el email
    }

    // Si el email no existe, devolver error genérico
    return back()->withErrors(['email' => 'Mail no registrado']);
}

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Sesión cerrada exitosamente.');
    } 
    
}


