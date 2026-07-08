<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstructorRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->user()?->tieneRol('administrador')) {
            $this->merge(['user_id' => null]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $instructor = $this->route('instructor');

        return [
            'nombre' => ['required', 'string', 'max:120'],
            'apellido' => ['required', 'string', 'max:120'],
            'cedula' => ['required', 'string', 'max:30', Rule::unique('instructors', 'cedula')->ignore($instructor?->id)],
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('instructors', 'user_id')->ignore($instructor?->id)],
            'telefono' => ['nullable', 'string', 'max:30'],
            'especialidad' => ['nullable', 'string', 'max:120'],
            'estado' => ['required', 'in:activo,inactivo'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'cedula.required' => 'La cedula es obligatoria.',
            'cedula.unique' => 'La cedula ya se encuentra registrada.',
            'user_id.exists' => 'Debe seleccionar un usuario valido para el instructor.',
            'user_id.unique' => 'El usuario seleccionado ya esta vinculado a otro instructor.',
        ];
    }
}
