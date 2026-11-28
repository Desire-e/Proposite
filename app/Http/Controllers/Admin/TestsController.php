<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Tema;
use App\Models\Test;
use App\Models\Pregunta;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Admin\StoreTestRequest;
use App\Http\Requests\Admin\UpdateTestRequest;



class TestsController extends Controller
{

    /**
     * Crear nuevo test
     */
    public function create(Request $request): Response 
    {
        Gate::authorize('create', Test::class);

        $oposicion = auth()->user()->comunidad();

        $temario = Tema::where('oposicion_id', $oposicion->id)
            ->select('id', 'bloque', 'numero_tema', 'titulo')
            ->orderBy('bloque')
            ->orderBy('numero_tema')
            ->get();

        return Inertia::render('admin/tests/create', [
            'oposicion' => $oposicion->only('id', 'nombre'),
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



    /**
     * Editar test existente
     */
    public function edit(Request $request, Test $test): Response
    {
        Gate::authorize('update', $test);

        $oposicion = auth()->user()->comunidad();

        $temario = Tema::where('oposicion_id', $oposicion->id)
            ->select('id', 'bloque', 'numero_tema', 'titulo')
            ->orderBy('bloque')
            ->orderBy('numero_tema')
            ->get();


        $preguntas = $test->preguntas()->with('opcionesRespuesta')->get();
        $preguntasOpciones = [];
        
        foreach($preguntas as $pregunta){
            $opciones=[];
            
            foreach($pregunta->opcionesRespuesta as $opcion) {
                $opciones[] = [
                    'oposicion' => $oposicion->only('id', 'nombre'),
                    'contenido' => $opcion->contenido,
                    'es_correcta' => $opcion->es_correcta,
                ];
            }

            $preguntasOpciones[] = [
                'id' => $pregunta->id,
                'enunciado' => $pregunta->enunciado,
                'opciones' => $opciones,
            ];
        }

        return Inertia::render('admin/tests/edit', [
            'oposicion' => $oposicion->nombre,
            'temario' => $temario,
            'test' => [
                'id' => $test->id, 
                'tema_id' => $test->temario_id, 
                'titulo' => $test->titulo,
                'descripcion' => $test->descripcion,
                'preguntasOpciones' => $preguntasOpciones,
            ]
        ]);
    }

    public function update(UpdateTestRequest $request, Test $test): RedirectResponse
    {
        Gate::authorize('update', $test);
    
        $validated = $request->validated();


        // ---- Actualiza test
        
        $test->update([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'temario_id' => $validated['tema_id'],
            // TODO. existe creado_por, pero no actualizado_por
        ]);

        // ---- Actualiza preguntas y opciones

        // Preguntas anteriores
        // Array de pregunta_id=>[datos_pregunta_opciones]
        $oldPreguntas = [];

        foreach ($test->preguntas()->with('opcionesRespuesta')->get() as $oldP) {
            $oldPreguntas[$oldP->id] = $oldP;
        }

        // Preguntas nuevas
        $newPreguntasId = [];

        foreach ($validated['preguntasOpciones'] as $validP) {
            $id = $validP['id'] ?? null;

            // Pregunta cambiada: comprueba que la pregunta nueva tiene id y 
            // existe en las antiguas existentes
            if ($id !== null && isset($oldPreguntas[$id])) {
                // Actualiza su enunciado
                $pregunta = $oldPreguntas[$id];
                $pregunta->update(['enunciado' => $validP['enunciado']]);
            } 
            // Pregunta nueva: Si no existía, se crea
            else {
                $pregunta = $test->preguntas()->create([
                    'test_id' => $test->id,
                    'enunciado' => $validP['enunciado'],
                ]);
            }

            // Almacena todas las preguntas cambiadas/nuevas
            $newPreguntasId[] = $pregunta->id;
            // Sincroniza sus opciones
            $this->sincronizarOpciones($pregunta, $validP['opciones']);
        }

        // Preguntas eliminadas 
        foreach ($oldPreguntas as $id => $pregunta) {
            if (!in_array($id, $newPreguntasId, true)) {
                $pregunta->delete();
            }
        }

        return to_route('admin.panel');
    }

    // TODO. Pasar a service
    private function sincronizarOpciones(Pregunta $pregunta, array $validPreguntasOpciones): void
    {
        $oldOpciones = [];
        foreach ($pregunta->opcionesRespuesta as $oldO) {
            $oldOpciones[$oldO->id] = $oldO;
        }

        $newOpcionesId = [];

        foreach ($validPreguntasOpciones as $validO) {
            $id = $validO['id'] ?? null;

            if ($id !== null && isset($oldOpciones[$id])) {
                $opcion = $oldOpciones[$id];
                $opcion->update([
                    'contenido' => $validO['contenido'],
                    'es_correcta' => $validO['es_correcta'],
                ]);
            } else {
                $opcion = $pregunta->opcionesRespuesta()->create([
                    'pregunta_id' => $pregunta->id,
                    'contenido' => $validO['contenido'],
                    'es_correcta' => $validO['es_correcta'],
                ]);
            }

            $newOpcionesId[] = $opcion->id;
        }

        foreach ($oldOpciones as $id => $opcion) {
            if (!in_array($id, $newOpcionesId, true)) {
                $opcion->delete();
            }
        }
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
