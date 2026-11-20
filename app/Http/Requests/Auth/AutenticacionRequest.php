<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AutenticacionRequest extends FormRequest
{
    /**
     * En login y registro el usuario aún no está autenticado
     */
    public function authorize(): bool { return true; }

    /**
     * Normalización antes de validar
     */
    protected function prepareForValidation(): void
    {
        // merge sustituye el valor original del campo email
        $this->merge(['email' => strtolower($this->email ?? '')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required', 
            'password' => 'required'            
        ];
    }

    /**
     * Renombrado de campos
     */
    public function attributes(): array
    {
        return [
            'email' => 'correo electrónico',
            'password' => 'contraseña',
        ];
    }
}
