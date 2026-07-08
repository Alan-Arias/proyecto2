<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rol = $this->route('role');

        return [
            'nombre' => ['required', 'string', 'max:80', Rule::unique('roles', 'nombre')->ignore($rol?->id)],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'El rol ya se encuentra registrado.',
        ];
    }
}
