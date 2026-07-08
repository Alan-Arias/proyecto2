<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120'],
            'ruta' => ['nullable', 'string', 'max:120'],
            'icono' => ['nullable', 'string', 'max:80'],
            'orden' => ['required', 'integer', 'min:1'],
            'activo' => ['nullable', 'boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del menu es obligatorio.',
            'orden.required' => 'El orden del menu es obligatorio.',
            'orden.min' => 'El orden debe ser mayor a cero.',
            'roles.required' => 'Debe seleccionar al menos un rol para el menu.',
        ];
    }
}
