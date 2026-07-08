<?php

namespace App\Http\Requests;

use App\Models\Instructor;
use App\Models\Estudiante;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');
        $idUsuario = $usuario?->id;

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($idUsuario)],
            'telefono' => ['nullable', 'string', 'max:30'],
            'password' => [$idUsuario ? 'nullable' : 'required', 'string', 'min:6'],
            'activo' => ['nullable', 'boolean'],
            'persona_tipo' => ['nullable', 'in:instructor,estudiante'],
            'persona_id' => ['nullable', 'integer'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $usuario = $this->route('usuario');
            $personaTipo = $this->input('persona_tipo');
            $personaId = $this->input('persona_id');

            if (! $personaTipo || ! $personaId) {
                return;
            }

            $persona = $personaTipo === 'instructor'
                ? Instructor::find($personaId)
                : Estudiante::find($personaId);

            if (! $persona) {
                $validator->errors()->add('persona_id', 'Debe seleccionar una persona valida.');
                return;
            }

            if ($persona->user_id && $persona->user_id !== $usuario?->id) {
                $validator->errors()->add('persona_id', 'La persona seleccionada ya esta vinculada a otro usuario.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electronico es obligatorio.',
            'email.email' => 'El correo electronico no es valido.',
            'email.unique' => 'El correo electronico ya se encuentra registrado.',
            'password.required' => 'La contrasena es obligatoria.',
            'password.min' => 'La contrasena debe tener al menos 6 caracteres.',
            'persona_tipo.in' => 'El tipo de persona seleccionado no es valido.',
            'roles.required' => 'Debe seleccionar al menos un rol.',
            'roles.*.exists' => 'El rol seleccionado no existe.',
        ];
    }
}
