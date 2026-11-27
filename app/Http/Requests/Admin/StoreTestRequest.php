<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;


class StoreTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tema_id' => ['required', 'uuid', Rule::exists('temarios', 'id')
                ->where('oposicion_id', $this->user()->comunidad()->id)],

            'titulo' => ['required', 'string', 'max:250'],
            
            'descripcion' => ['required', 'string'],
        
            'preguntasOpciones' => ['required', 'array', 'min:5', 'max:15'],    
        
            // para cada elemento del array preguntas, el campo enunciado 
            // debe estar presente y ser un string
            'preguntasOpciones.*.enunciado' => ['required', 'string'],
            'preguntasOpciones.*.opciones' => ['required', 'array', 'min:3', 'max:4'],
            'preguntasOpciones.*.opciones.*.contenido' => ['required', 'string'],
            'preguntasOpciones.*.opciones.*.es_correcta' => ['required', 'boolean'],
        ];
    }

    /**
     *  Devuelve una lista de funciones que Laravel ejecuta después 
     * de aplicar las reglas de rules().
     */
    public function after(): array
    {
        return [
            $this->opcionCorrectaPorPregunta(...),
        ];
    }

    public function opcionCorrectaPorPregunta(Validator $validator): void
    {
        $preguntas = $this->input('preguntasOpciones', []);

        foreach ($preguntas as $i => $pregunta) {
            $opciones = $pregunta['opciones'] ?? [];
            $correctas = 0;

            foreach ($opciones as $opcion) {
                if (!empty($opcion['es_correcta'])) {
                    $correctas++;
                }
            }

            if ($correctas !== 1) {
                $validator->errors()->add(
                    "preguntasOpciones.$i.opciones",
                    'Marca exactamente una respuesta correcta.'
                );
            }
        }
    }

    public function attributes(): array
    {
        return [
            'tema_id' => 'tema',
            'titulo' => 'título',
            'descripcion' => 'descripción',
            'preguntasOpciones' => 'preguntas',
            'preguntasOpciones.*.enunciado' => 'enunciado de la pregunta',
            'preguntasOpciones.*.opciones' => 'opciones',
            'preguntasOpciones.*.opciones.*.contenido' => 'contenido de la opción',
        ];
    }

    public function messages(): array
    {
        return [
            // General
            'tema_id.required' => 'El :attribute es obligatorio',
            'titulo.required' => 'El :attribute es obligatorio',
            'titulo.max' => 'El :attribute no puede superar los :max caracteres.',
            'descripcion.required' => 'La :attribute es obligatoria',

            // Preguntas
            'preguntasOpciones.min' => 'El test debe tener al menos :min :attribute.',
            'preguntasOpciones.max' => 'El test debe tener al menos :max :attribute.',

            // Opciones
            'preguntasOpciones.*.enunciado.required' => 'El :attribute es obligatorio.',
            'preguntasOpciones.*.opciones.min' => 'Cada pregunta necesita al menos :min :attribute.',
            'preguntasOpciones.*.opciones.max' => 'Cada pregunta admite como máximo :max :attribute.',
            'preguntasOpciones.*.opciones.*.contenido.required' => 'El :attribute es obligatorio.',
        ];
    }
}
