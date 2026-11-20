<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SelectOposicionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'oposicion_id' => [
                'required', 
                'uuid', 
                Rule::exists('oposiciones', 'id')->withoutTrashed()
            ],
        ];
    }

    public function attributes(): array
    {
        return ['oposicion_id' => 'oposición'];
    }
}