<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegistroRequest extends FormRequest
{
    public function authorize(): bool { return true; }


    protected function prepareForValidation(): void
    {
        $this->merge(['email' => strtolower($this->email ?? '')]);
    }


    public function rules(): array
    {
        return [
            'oposicion_id' => [
                'required',
                'uuid',
                'exists:oposiciones,id',
            ],
            'name' => [
                'required', 
                'string', 
                'max:150', 
                'unique:usuarios,name'
            ],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:150', 
                'unique:usuarios,email'
            ],
            'password' => [
                'required', 
                'string',
                'min:8'
            ],
            // 'rol'=> ['nullable',], //automatico
            'fecha_convocatoria' => [
                'nullable', 
                'date',
                'after:today'
            ],
            'acepta_terminos' => [
                'accepted', // indica que ha aceptado; admite valores como true, 1, "yes" y "on" 
            ],
        ];
    }
    

    public function attributes(): array
    {
        return [
            'oposicion_id' => 'oposición',
            'name' => 'nombre de usuario',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'fecha_convocatoria' => 'fecha de convocatoria',
        ];
    }


    /**
     * Mensajes de error específicos
     */
    public function messages(): array
    {
        return [
            'oposicion_id.uuid' => 'El identificador de la oposición no es válido.',
            'oposicion_id.exists' => 'La oposición seleccionada no existe.',
            'acepta_terminos.accepted' => 'Debe de aceptar los términos y condiciones.',
        ];
    }
}
