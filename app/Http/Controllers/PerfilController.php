<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePerfilRequest;
use App\Services\PerfilService;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;


class PerfilController extends Controller
{

    /**
     * Constructor.
     * @param PerfilService 
     * Laravel crea el servicio y lo entrega.
     */
    public function __construct(private PerfilService $perfil) {}


    /**
     * Perfil del propio usuario autenticado
     */
    public function show(Request $request): Response
    {
        return Inertia::render('perfil/show', [
            'user' => $this->perfil->datosPerfil($request->user()),
            'es_propio' => true,
        ]);
    }


    /**
     * Actualiza los datos del usuario
     */
    public function edit(Request $request): Response 
    {
        $user = $request->user();

        return Inertia::render('perfil/edit', [
            'user' => [
                'name' => $user->name ?? null,
                'email'=> $user->email ?? null,
                'biografia'=> $user->biografia ?? null,
                'fecha_convocatoria'=> $user->fecha_convocatoria?->format('Y-m-d') ?? null,
            ],
        ]);
    }

    public function update(UpdatePerfilRequest $request): RedirectResponse
    {
        $user = $request->user(); // usuario autenticado que hizo la request

        $user->update($request->validated());

        return to_route('perfil.show')->with('aviso', [
            'tipo' => 'success',
            'mensaje' => 'Perfil actualizado correctamente.',
        ]);
    }
}
