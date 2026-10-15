<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra página login
     */

    public function login(Request $request): Response {
        // Devuelve la página de Inertia cuyo nombre es auth/login (resources/js/pages/auth/login.tsx)
        return Inertia::render('auth/login');
    }

    /**
     * Maneja request de login
     */
    
    public function authenticate(Request $request): RedirectResponse 
    {
        // -- Validar usuario --

        $validated = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ],);

        // -- Autenticar usuario --

        if (!Auth::attempt($validated)) {
            return back()->withErrors([
                'password' => 'El usuario y/o la contraseña no son correctos',
            ])->onlyInput('email'); // qué datos del formulario se conservan al volver a la página

        } 

        // -- Regenerar sesión --

        $request->session()->regenerate();

        // -- Redirección --

        return redirect()->intended(route('dashboard'));
    }


    /**
     * Maneja request de logout
     */

    public function logout(Request $request): RedirectResponse
    {
        // -- Cerrar e invalidar la sesión, regenear CSRF  --
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // -- Redirección --

        return redirect()->route('welcome');
    }
}
