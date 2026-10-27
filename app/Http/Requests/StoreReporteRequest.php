<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['motivo' => ['required', 'string', 'max:1000']];
    }

    public function messages(): array
    {
        return ['motivo.required' => 'Indica el motivo del reporte.'];
    }
}
