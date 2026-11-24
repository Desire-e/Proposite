<?php

namespace App\Services;

use App\Models\IntentoTest;
use App\Models\Pregunta;
use App\Models\Test;
use App\Models\Usuario;

use Illuminate\Validation\ValidationException;

class IntentosTestService
{
    /**
     * Datos para realizar un intento del @param test
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
     * Corrige el intento, lo guarda y deja la corrección en sesión
     * para mostrarla en la página de resultado.
     *
     * @param test realizado
     * @param usuario que hizo el intento
     * @param respuestas marcadas
     */
    public function registrar(Test $test, Usuario $usuario, array $respuestas): IntentoTest
    {
        $resultados = $this->corregir($test, $usuario, $respuestas);

        $intento = IntentoTest::create([
            'usuario_id' => $usuario->id,
            'test_id' => $test->id,
            'nota' => $resultados['nota']['actual'],
        ]);

        // Correción mostrada temporalmente (sesión flash), ligado al intento
        session()->put("correccion_intento.{$intento->id}", $resultados);

        return $intento;
    }


    // ====================
    // Correción (cálculos)
    // ====================


    /**
     * @return array con el test, la corrección de cada pregunta
     * y la nota actual y anterior
     */
    public function corregir(Test $test, ?Usuario $usuario, array $respuestas): array
    {
        // Preguntas del test, con sus opciones
        $preguntas = $test->preguntas()
            ->with('opcionesRespuesta')
            ->get()
            ->keyBy('id'); // convierte una colección en otra cuyas claves son el id

        // --- Opciones seleccionadas ---

        $seleccionadas = [];

        foreach ($respuestas as $r) {
            $seleccionadas[$r['pregunta_id']] = $r['respuesta_id'];
        }
        
        // --- Validación de preguntas y opciones seleccionadas ---
        // Si la pregunta de la opcion seleccionada no existe en las preguntas del test
        // Si la opcion seleccionada no existe entre las opciones de la pregunta
        
        foreach($seleccionadas as $preguntaId => $respuestaId) {

            $pregunta = $preguntas->firstWhere('id', $preguntaId);

            if(!$pregunta || !$pregunta->opcionesRespuesta->contains('id', $respuestaId)){
                throw ValidationException::withMessages(
                    ['respuestas' => 'Respuesta no válida para este test.']
                );
            }
        }

        // --- Corrección ---
        
        // Array de pregunta-respuesta corregidas
        $correcciones = [];
        // Número de correctas
        $correctas = 0;

        foreach($preguntas as $preg) {

            // Opción seleccionada o null si no respondió
            $seleccionada = $seleccionadas[$preg->id] ?? null;

            // Todas las opciones corregidas y número de correctas
            $opciones = [];

            foreach($preg->opcionesRespuesta as $opc) {

                if ($opc->id === $seleccionada && $opc->es_correcta) $correctas++;

                $opciones[] = [
                    'id' => $opc->id,
                    'contenido' => $opc->contenido,
                    'es_correcta' => (bool) $opc->es_correcta,
                    'selected' => $opc->id === $seleccionada
                ];
            }

            // Correcciones
            $correcciones[] = [
                'pregunta' => $preg->only('id', 'enunciado'),
                'sin_contestar' => $seleccionada === null,
                'opciones' => $opciones,
            ];
        }

        // --- Calcular nota sobre 10 y obtener nota anterior ---
        // Se consulta ANTES de crear el nuevo intento; si no, la
        // "anterior" sería la que se acaba de guardar

        $nota = $this->calcularNota($correctas, $preguntas->count());

        $notaAnterior = $usuario 
            ? $test->intentosTest()->where('usuario_id', $usuario->id)->latest()->value('nota')
            : null;

        return [
            'test' => $test->only('id', 'titulo', 'temario_id'),
            'correccion' => $correcciones,
            'nota' => [
                'actual' => number_format($nota, 2),
                'anterior' => $notaAnterior !== null ? number_format($notaAnterior, 2) : null,
            ],
        ];
    }    


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