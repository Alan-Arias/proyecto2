<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OfertaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $oferta = $this->route('oferta');

        return [
            'course_id' => ['required', 'exists:courses,id'],
            'instructor_id' => ['nullable', 'exists:instructors,id'],
            'codigo' => ['required', 'string', 'max:30', Rule::unique('offers', 'codigo')->ignore($oferta?->id)],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'cupos' => ['required', 'integer', 'min:1'],
            'estado' => ['required', 'in:activo,finalizado,cancelado'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.required' => 'Debe seleccionar un curso.',
            'course_id.exists' => 'El curso seleccionado no existe.',
            'codigo.required' => 'El codigo de la oferta es obligatorio.',
            'codigo.unique' => 'El codigo de oferta ya se encuentra registrado.',
            'fecha_inicio.required' => 'Debe indicar la fecha de inicio.',
            'fecha_fin.required' => 'Debe indicar la fecha final.',
            'fecha_fin.after_or_equal' => 'La fecha final no puede ser anterior a la fecha de inicio.',
            'cupos.required' => 'Debe indicar la cantidad de cupos.',
        ];
    }
}
