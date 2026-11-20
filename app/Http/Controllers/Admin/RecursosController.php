<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Admin\RecursoRequest;

use App\Models\Recurso;
use App\Models\Tema;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request; 

class RecursosController extends Controller
{
    /**
     * ADMIN.
     * Añade un recurso a un tema de la oposición 
     * que modera el administrador
     */
    public function store(RecursoRequest $request, Tema $tema): RedirectResponse
    {
        Gate::authorize('create', [Recurso::class, $tema]);

        Recurso::create([
            'titulo' => $request->validated()['titulo'],
            'recurso_url' => $request->validated()['recurso_url'],
            'temario_id' => $tema->id,
            'subido_por' => $request->user()->id,
        ]);

        return back();
    }


    public function delete(Recurso $recurso): RedirectResponse 
    {
        Gate::authorize('delete', $recurso);

        $recurso->delete(); // borrado lógico (SoftDeletes)

        return back();
    }


    public function update(RecursoRequest $request, Recurso $recurso): RedirectResponse
    {
        Gate::authorize('update', $recurso);

        $recurso->update($request->validated());

        return back();
    }

}
