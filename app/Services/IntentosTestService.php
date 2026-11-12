<?php

namespace App\Services;

use App\Models\Test;
use App\Models\Pregunta;
use App\Models\Usuario;
use Illuminate\Validation\ValidationException;

class IntentosTestService
{
    /**
     * @param test que quiere realizar
     */
    public function createIntentoData(Test $test): array
    {
        // carga relaciones sobre un modelo
        $test->load('preguntas.opcionesRespuesta');

        return [
            'test' => [
                'id' => $test->id,
                'titulo' => $test->titulo,
                'preguntas' => $this->preguntasTest($test),
            ],
        ];
    }

    /**
     * @param test del intento realizado
     * @param usuarioId que hizo el intento
     * @param respuestas marcadas
     */
    public function storeIntentoData(Test $test, int|string $usuarioId, array $respuestas): array
    {
        // Preguntas del test, con sus opciones
        // keyBy('id') convierte una colección en otra cuyas claves son el valor del campo indicado
        $preguntas = $test->preguntas()
            ->with('opcionesRespuesta')
            ->get()
            ->keyBy('id');

        $respuestasCorregidas = [];
        $correctas = 0;

        foreach ($respuestas as $r) {
            // --- Validación extra ---
            // La pregunta debe pertenecer al test
            // La opción debe pertenecer a la pregunta

            $pregunta = $preguntas->get($r['pregunta_id']);
            $opcion = $pregunta?->opcionesRespuesta->firstWhere('id', $r['respuesta_id']);
            
            if (!$pregunta || !$opcion) {
                throw ValidationException::withMessages(
                    ['respuestas' => 'Respuesta no válida para este test.']
                    );
            }

            // --- Corrección ---
            // Número de correctas
            // Preguntas correctas (1) e incorrectas (0)

            $correctas += $opcion->es_correcta ? 1 : 0;

            $correcciones[] = [
                'pregunta' => $pregunta->only('id', 'enunciado'),
                'opciones' => $pregunta->opcionesRespuesta
                    ->map(fn ($o) => [
                        'id' => $o->id,
                        'contenido' => $o->contenido,
                        'es_correcta' => (bool) $o->es_correcta,
                        'selected' => $o->id === $opcion->id,
                    ])
                    ->all(),
            ];
        }

        // --- Calcular nota sobre 10 y obtener nota anterior ---

        $nota = $this->calcularNota($correctas, $preguntas->count());

        $notaAnterior = $test->intentosTest()
            ->where('usuario_id', $usuarioId)
            ->latest()
            ->value('nota');

        return [
            'test' => $test->only('id', 'titulo', 'temario_id'),
            'correccion' => $correcciones,
            'nota' => [
                'actual' => number_format($nota, 2),
                'anterior' => $notaAnterior ? number_format($notaAnterior, 2) : null
            ]
        ];
    }



    // ====================
    // Cálculos
    // ====================

    private function calcularNota(int $correctas, int $total): float
    {
        return $total > 0 
            ? round(($correctas * 10) / $total, 2) 
            : 0.0;
    }



    // ====================
    // Consultas
    // ====================

    /**
     * @return array de preguntas del test 
     * con id, enunciado y opciones
     */
    private function preguntasTest(Test $test): array
    {
        return $test->preguntas->map(fn ($pregunta) => [
            'id' => $pregunta->id,
            'enunciado' => $pregunta->enunciado,
            'opciones' => $this->opcionesPregunta($pregunta),
        ])->all();
    }

    /**
     * @return array de opciones de la pregunta 
     * con id y contenido 
     */
    private function opcionesPregunta(Pregunta $pregunta): array
    {
        return $pregunta->opcionesRespuesta->map(fn ($opcion) => [
            'id' => $opcion->id,
            'contenido' => $opcion->contenido,
        ])->all();
    }
}