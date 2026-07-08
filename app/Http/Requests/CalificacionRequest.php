<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalificacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enrollment_id' => ['required', 'exists:enrollments,id'],
            'nota' => ['required', 'numeric', 'min:0', 'max:100'],
            'observacion' => ['nullable', 'string', 'max:180'],
        ];
    }

    public function messages(): array
    {
        return [
            'enrollment_id.required' => 'Debe seleccionar una inscripcion.',
            'nota.required' => 'La nota es obligatoria.',
            'nota.min' => 'La nota debe estar entre 0 y 100.',
            'nota.max' => 'La nota debe estar entre 0 y 100.',
        ];
    }
}
