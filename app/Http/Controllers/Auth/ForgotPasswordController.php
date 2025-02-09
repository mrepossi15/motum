<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Muestra el formulario para solicitar el enlace de restablecimiento de contraseña.
     */
    public function showLinkRequestForm()
    {
        return view('auth.email-password'); // Vista para solicitar el correo
    }

    /**
     * Envía el enlace de restablecimiento al correo proporcionado.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            // Redirigir al login con un mensaje de éxito
            return redirect()->route('login')->with('status', __($status));
        }

        // Retornar con error si algo falla
        return back()->withErrors(['email' => __($status)]);
    }
}