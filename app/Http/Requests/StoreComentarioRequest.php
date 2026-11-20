<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComentarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La autorización la hace PublicacionPolicy via Gate en ComentariosController
        return true;
    }

    public function rules(): array
    {
        return ['contenido' => ['required', 'string', 'max:2000']];
    }

    public function messages(): array
    {
        return ['contenido.required' => 'Debe escribir antes de comentar.'];
    }
}
