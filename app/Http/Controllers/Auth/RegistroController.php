<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Oposicion;
use App\Enums\Rol;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\Auth\RegistroRequest;
// respuesta que devuelve Inertia cuando se renderiza una página.
use Inertia\Response;
// respuesta que indica al navegador que debe dirigirse a otra URL.
use Illuminate\Http\RedirectResponse; 
use Inertia\Inertia;

class RegistroController extends Controller {
    /**
     * Muestra página de registro
     */
    public function create(): Response 
    {
        return Inertia::render('auth/register', [
            'oposiciones' => Oposicion::select('id', 'nombre')->get()
        ]);
    }

    public function store(RegistroRequest $request): RedirectResponse 
    { 
        // Datos validados sin acepta_terminos

        // safe() method returns an instance of Illuminate\Support\ValidatedInput. 
        // This object exposes only, except, and all methods to retrieve a subset 
        // of the validated data or the entire array of validated data:
        $validated = $request->safe()->except('acepta_terminos');

        // Procesamiento de datos
        $validated['password'] = Hash::make($validated['password']);
        $validated['rol'] = Rol::OPOSITOR->value;

        // Creación de usuario válido 
        $usuario = Usuario::create($validated);

        // Inicio de sesión
        Auth::login($usuario);
        $request->session()->regenerate();

        // Redirección
        // return redirect()->route('dashboard')->with('success','Usuario registrado correctamente.');
        return to_route('dashboard');
    }
}
