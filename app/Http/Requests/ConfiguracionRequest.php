<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfiguracionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $configuracion = $this->route('configuracion');

        return [
            'clave' => ['required', 'string', 'max:120', Rule::unique('settings', 'clave')->ignore($configuracion?->id)],
            'valor' => ['nullable', 'string'],
            'tipo' => ['required', 'in:texto,numero,booleano'],
            'descripcion' => ['nullable', 'string', 'max:180'],
        ];
    }

    public function messages(): array
    {
        return [
            'clave.required' => 'La clave de configuracion es obligatoria.',
            'clave.unique' => 'La clave de configuracion ya existe.',
            'tipo.required' => 'Debe seleccionar el tipo de configuracion.',
        ];
    }
}
