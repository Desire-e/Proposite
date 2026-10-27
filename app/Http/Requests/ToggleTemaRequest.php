<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ToggleTemaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'checked' => ['required', 'boolean'],
            'tema' => [
                'required',
                // el tema debe existir y ser de la oposición del usuario
                Rule::exists('temarios', 'id')
                    ->where('oposicion_id', $this->user()->oposicion_id),
            ],
        ];
    }
}
