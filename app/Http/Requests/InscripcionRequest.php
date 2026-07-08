<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InscripcionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'offer_id' => ['required', 'exists:offers,id'],
            'fecha_inscripcion' => ['required', 'date'],
            'forma_pago' => [$this->isMethod('post') ? 'required' : 'nullable', 'in:efectivo,qr,cuotas'],
            'monto_pagado' => ['nullable', 'numeric', 'min:0'],
            'referencia' => ['nullable', 'string', 'max:120'],
            'numero_cuotas' => ['nullable', 'integer', 'min:2'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Debe seleccionar un estudiante.',
            'offer_id.required' => 'Debe seleccionar una oferta o curso.',
            'fecha_inscripcion.required' => 'Debe indicar la fecha de inscripcion.',
            'forma_pago.required' => 'Debe seleccionar una forma de pago.',
            'numero_cuotas.min' => 'El pago en cuotas debe tener dos o mas cuotas.',
        ];
    }
}
