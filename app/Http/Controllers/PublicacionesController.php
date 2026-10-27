<?php

namespace App\Http\Controllers;


use App\Models\Publicacion;
use Illuminate\Support\Facades\Gate;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StorePublicacionRequest;


class PublicacionesController extends Controller
{

    /**
     * Mostrar una publicacion con sus comentarios
     */
    public function show(Request $request, Publicacion $publicacion): Response
    {
        // Gate::authorize consulta la policy y lanza el 403 si deniega
        Gate::authorize('view', $publicacion);
        // abort_if($publicacion->oposicion_id !== $request->user()->oposicion_id, 403);


        return Inertia::render('foro/publicaciones/show', [
            'usuario_autor' => $publicacion->usuario->only([
                'id', 
                'name'
            ]),
            'publicacion' => $publicacion->only([
                'id', 
                'oposicion_id', 
                'contenido', 
                'created_at',
            ]),
            'me_sirvio' => $publicacion->meSirvio()->count(),
            'me_sirvio_activo' => $publicacion->meSirvio()
                ->where('usuarios.id', $request->user()->id)
                ->exists(),

            'comentarios' => $publicacion->comentarios()
                ->with('usuario:id,name')
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }


    /**
     * Publicar
     */
    public function store(StorePublicacionRequest $request): RedirectResponse
    {
        $user = $request->user();

        Publicacion::create([
            'usuario_id' => $user->id,
            'oposicion_id' => $user->oposicion_id,
            'contenido' => $request->validated()['contenido'],
        ]);

        return back();
    }


    /**
     * Cambiar "me sirvió" / like
     */
    public function toggleMeSirvio(Request $request, Publicacion $publicacion): RedirectResponse
    {
        $user = $request->user();

        Gate::authorize('view', $publicacion);

        // toggle() es un método de relaciones belongsToMany que invierte el 
        // estado de la relación entre dos modelos en la tabla pivote. 
        // Si el ID ya está relacionado, lo desrelaciona (borra fila). 
        // Si no, lo relaciona (inserta fila) 
        $publicacion->meSirvio()->toggle($user->id);

        return back();
    }


    public function delete(Publicacion $publicacion): RedirectResponse
    {
        Gate::authorize('delete', $publicacion);

        $publicacion->delete(); // soft delete: rellena deleted_at

        return to_route('foro.index');
    }

}
