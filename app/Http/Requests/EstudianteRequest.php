<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstudianteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $estudiante = $this->route('estudiante');
        $idUsuario = $estudiante?->user_id;

        return [
            'nombre' => ['required', 'string', 'max:120'],
            'apellido' => ['required', 'string', 'max:120'],
            'cedula' => ['required', 'string', 'max:30', Rule::unique('students', 'cedula')->ignore($estudiante?->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($idUsuario)],
            'password' => ['nullable', 'string', 'min:6'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:180'],
            'fecha_nacimiento' => ['nullable', 'date'],
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
            'email.required' => 'El correo electronico es obligatorio.',
            'email.email' => 'El correo electronico no es valido.',
            'email.unique' => 'El correo electronico ya se encuentra registrado.',
            'password.min' => 'La contrasena debe tener al menos 6 caracteres.',
            'estado.required' => 'Debe seleccionar el estado del estudiante.',
        ];
    }
}
