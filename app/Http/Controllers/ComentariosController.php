<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;

use App\Models\Publicacion;
use App\Models\Comentario;

use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreComentarioRequest;


class ComentariosController extends Controller
{
    /**
     * Comentar
     * Al poner @param StoreComentarioRequest como tipo del parámetro, 
     * se validan los datos de petición antes de entrar al método
     */
    public function store(StoreComentarioRequest $request, Publicacion $publicacion): RedirectResponse
    {
        Gate::authorize('comment', $publicacion);
        
        Comentario::create([
            'usuario_id' => $request->user()->id,
            'publicacion_id' => $publicacion->id,
            'contenido'  => $request->validated()['contenido'],
        ]);

        return back();
    }

    /**
     * TODO. Eliminar comentario
     */

}
