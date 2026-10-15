<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Oposicion;
use App\Enums\Rol;
use Illuminate\Auth\Events\Registered;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

use Illuminate\Http\Request;
// respuesta que devuelve Inertia cuando se renderiza una página.
use Inertia\Response;
// respuesta que indica al navegador que debe dirigirse a otra URL.
use Illuminate\Http\RedirectResponse; 
use Inertia\Inertia;

class RegisteredUserController extends Controller {
    /**
     * Muestra página de registro
     */

    public function create(): Response 
    { 
        return Inertia::render('auth/register', [
            'oposiciones' => Oposicion::all(),
        ]);
    }


    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function store(Request $request): RedirectResponse 
    { 
        // -- Validación de usuario -- 
        
        $validated = $request->validate([
            'oposicion_id' => ['required','uuid','exists:oposiciones,id',],
            'name' => ['required', 'string', 'max:150', 'unique:usuarios,name',],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:usuarios,email',],
            'password' => ['required', 'max:20', 'min:8',],
            'rol'=> ['nullable',], //automatico
            'fecha_convocatoria' => ['nullable', 'date',], // automatico 
            'acepta_terminos' => [
                'accepted', // indica que ha aceptado; admite valores como true, 1, "yes" y "on" 
            ],
        ],
        [
            'oposicion_id.required' => 'El campo oposición es obligatorio.',
            'oposicion_id.uuid' => 'El identificador de la oposición no es válido.',
            'oposicion_id.exists' => 'La oposicisón seleccionada no existe.',
            'acepta_terminos.accepted' => 'Debe de aceptar los términos y condiciones.',
        ]);

        // -- Procesamiento de datos -- 

        $validated['password'] = Hash::make($validated['password']);
        $validated['rol'] = Rol::OPOSITOR->value;
        $validated['fecha_convocatoria'] = $validated['fecha_convocatoria'] ?? null;

        // -- Creación de usuario válido -- 

        $usuario = Usuario::create($validated);

        // Dispara un evento de Laravel (aviso que el resto de la aplicación puede 
        // escuchar para reaccionar) que indica que se ha registrado un usuario nuevo.
        // Puede tener listeners asociados.
        // event(new Registered($usuario));

        // -- Inicio de sesión -- 

        Auth::login($usuario);

        // -- Redirección -- 
        
        // return redirect()->route('dashboard')->with('success','Usuario registrado correctamente.');
        return to_route('welcome');
    }
}
