<?php

namespace App\Http\Controllers;

use App\Models\Usuario;

use App\Services\PerfilService;
use Illuminate\Support\Facades\Gate;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;


class OpositoresController extends Controller
{

    public function __construct(private PerfilService $perfil) {}

    /**
     * Muestra el perfil de otro opositor
     */
    public function show(Request $request, Usuario $usuario): Response|RedirectResponse
    {
        // Abrir con tu propio id por esta URL redirige a tu propio /perfil
        if ($usuario->is($request->user())) {
            return to_route('perfil.show');
        }

        Gate::authorize('view', $usuario);
        // abort_if($usuario->rol === Rol::ADMIN || $usuario->suspendido, 404);

        return Inertia::render('perfil/show', [
            'user' => $this->perfil->datosPerfil($usuario),
            'es_propio' => false,
        ]);
    }
}
