<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIntentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Validar que tenga los objetos necesarios pregunta_id, respuesta_id
            'respuestas' => ['required', 'array'],
            // sin distinct, se puede enviar la misma pregunta varias veces y sumar aciertos de más
            'respuestas.*.pregunta_id' => ['required', 'distinct'],
            'respuestas.*.respuesta_id' => ['required'],
        ];
    }
}