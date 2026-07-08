<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsistenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enrollment_id' => ['required', 'exists:enrollments,id'],
            'fecha' => ['required', 'date'],
            'estado' => ['required', 'in:presente,ausente,licencia'],
            'observacion' => ['nullable', 'string', 'max:180'],
        ];
    }

    public function messages(): array
    {
        return [
            'enrollment_id.required' => 'Debe seleccionar una inscripcion.',
            'fecha.required' => 'Debe indicar la fecha de asistencia.',
            'estado.required' => 'Debe seleccionar el estado de asistencia.',
        ];
    }
}
