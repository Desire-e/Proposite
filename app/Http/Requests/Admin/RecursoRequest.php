<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RecursoRequest extends FormRequest
{
    // El middleware 'admin' ya protege la ruta
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'      => ['required', 'string', 'max:255'],
            'recurso_url' => ['required', 'url:http,https', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return ['titulo' => 'título', 'recurso_url' => 'enlace'];
    }
}