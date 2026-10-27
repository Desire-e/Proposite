<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdatePerfilRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        // en un FormRequest, equivale a $request->user()
        $user = $this->user();

        $oldFechaConvocatoria = $user->fecha_convocatoria?->format('Y-m-d');
        
        return [
            'name' => [
                'required', 
                'string',
                'max:150',
                Rule::unique('usuarios', 'name')->ignore($user->id),
            ],
            'biografia' => [
                'nullable', 
                'string',
                'max:500'
            ],
            'fecha_convocatoria' => [
                'nullable', 
                'date',
                // exige fecha futura solo si el usuario la ha cambiado
                Rule::when($this->input('fecha_convocatoria') !== $oldFechaConvocatoria, 
                    ['after:today']),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre de usuario',
            'biografia' => 'biografía',
            'fecha_convocatoria' => 'fecha de convocatoria',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_convocatoria.after' => 'La fecha de convocatoria debe ser posterior a hoy.',
        ];
    }

}
