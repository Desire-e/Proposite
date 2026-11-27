<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Tema;
use App\Models\Test;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Admin\StoreTestRequest;


class TestsController extends Controller
{
    public function create(Request $request): Response 
    {
        $oposicion = auth()->user()->comunidad();

        $temario = Tema::where('oposicion_id', $oposicion->id)
            ->select('id', 'bloque', 'numero_tema', 'titulo')
            ->orderBy('bloque')
            ->orderBy('numero_tema')
            ->get();

        return Inertia::render('admin/tests/create', [
            'oposicion' => $oposicion->nombre,
            'temario' => $temario 
        ]);
    }
    

    public function store(StoreTestRequest $request): RedirectResponse
    {
        Gate::authorize('create', Test::class);

        $validated = $request->validated();

        $test = Test::create([
            'creado_por'  => $request->user()->id,
            'temario_id'  => $validated['tema_id'],
            'titulo'      => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
        ]);

        foreach ($validated['preguntasOpciones'] as $datosPregunta) {
            $pregunta = $test->preguntas()->create([
                'enunciado' => $datosPregunta['enunciado'],
            ]);

            foreach ($datosPregunta['opciones'] as $datosOpcion) {
                $pregunta->opcionesRespuesta()->create([
                    'contenido'   => $datosOpcion['contenido'],
                    'es_correcta' => $datosOpcion['es_correcta'],
                ]);
            }
        }

        return to_route('admin.panel');
    }


    public function edit(Request $request, Test $test): Response
    {
        // $user = $request->user();

        // return Inertia::render('perfil/edit', [
        //     'user' => [
        //         'name' => $user->name ?? null,
        //         'email'=> $user->email ?? null,
        //         'biografia'=> $user->biografia ?? null,
        //         'fecha_convocatoria'=> $user->fecha_convocatoria?->format('Y-m-d') ?? null,
        //     ],
        // ]);

        
        return Inertia::render('admin/tests/edit', [
            // 'oposiciones' => Oposicion::select('id', 'nombre')->get()
        ]);
    }

    public function update(Request $request, Test $test): RedirectResponse
    {
        // $user = $request->user(); // usuario autenticado que hizo la request

        // Gate::authorize('update', $user);

        // $user->update($request->validated());

        // return to_route('perfil.show')->with('aviso', [
        //     'tipo' => 'success',
        //     'mensaje' => 'Perfil actualizado correctamente.',
        // ]);
    }

    /**
     * Eliminar test existente
     */
    public function delete(Request $request, Test $test): RedirectResponse
    {
        Gate::authorize('delete', $test);
        $test->delete(); 
        return back();
    }
}
