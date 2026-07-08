<?php

namespace App\Http\Requests;

use App\Models\Inscripcion;
use Illuminate\Foundation\Http\FormRequest;

class PagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enrollment_id' => ['required', 'exists:enrollments,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'monto' => ['required', 'numeric', 'min:1'],
            'fecha_pago' => ['required', 'date'],
            'referencia' => ['nullable', 'string', 'max:120'],
            'comprobante' => ['nullable', 'string', 'max:180'],
            'observacion' => ['nullable', 'string', 'max:180'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $inscripcion = Inscripcion::find($this->input('enrollment_id'));

            $pagoActual = $this->route('pago');
            $saldoDisponible = (float) ($inscripcion?->saldo_pendiente ?? 0) + (float) ($pagoActual?->monto ?? 0);

            if ($inscripcion && (float) $this->input('monto') > $saldoDisponible) {
                $validator->errors()->add('monto', 'El monto pagado no puede ser mayor al saldo pendiente.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'enrollment_id.required' => 'Debe seleccionar una inscripcion.',
            'payment_method_id.required' => 'Debe seleccionar un metodo de pago.',
            'monto.required' => 'El monto pagado es obligatorio.',
            'monto.min' => 'El monto pagado debe ser mayor a cero.',
            'fecha_pago.required' => 'Debe indicar la fecha de pago.',
        ];
    }
}
