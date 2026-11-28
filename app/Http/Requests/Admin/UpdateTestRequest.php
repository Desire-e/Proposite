<?php

namespace App\Http\Requests\Admin;

class UpdateTestRequest extends StoreTestRequest
{
    public function rules(): array
    {
        // Se añade: id de la pregunta, id de la opción de respuesta
        return array_merge(parent::rules(), [
            'preguntasOpciones.*.id' => ['nullable', 'uuid'],
            'preguntasOpciones.*.opciones.*.id' => ['nullable', 'uuid'],
        ]);
    }
}