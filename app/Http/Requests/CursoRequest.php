<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:140'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'duracion_horas' => ['required', 'integer', 'min:1'],
            'tipo_licencia' => ['required', 'string', 'max:80'],
            'tema_visual' => ['required', 'in:ninos,jovenes,adultos'],
            'estado' => ['required', 'in:disponible,activo,finalizado,cancelado'],
            'materias' => ['nullable', 'array'],
            'materias.*' => ['exists:subjects,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del curso es obligatorio.',
            'precio.required' => 'El precio del curso es obligatorio.',
            'duracion_horas.required' => 'Debe indicar la duracion del curso.',
            'tipo_licencia.required' => 'Debe indicar el tipo de licencia.',
            'tema_visual.required' => 'Debe seleccionar un tema visual.',
            'estado.required' => 'Debe seleccionar el estado del curso.',
        ];
    }
}
