<?php

declare(strict_types=1);

namespace Src\Tecnico\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTecnicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('cedula')) $data['cedula'] = $this->cedula;
        if ($this->has('nombre')) $data['nombre'] = $this->nombre;
        if ($this->has('apellido')) $data['apellido'] = $this->apellido;
        if ($this->has('telefono')) $data['telefono'] = $this->telefono;
        if ($this->has('email')) $data['email'] = $this->email;
        if ($this->has('especialidad')) $data['especialidad'] = $this->especialidad;
        if ($this->has('activo')) $data['activo'] = $this->activo;

        $this->merge($data);
    }

    public function rules(): array
    {
        $tecnicoId = $this->route('id') ?? $this->route('tecnico');

        return [
            'cedula' => 'sometimes|string|max:20|unique:tecnicos,cedula,' . $tecnicoId,
            'nombre' => 'sometimes|string|max:100',
            'apellido' => 'sometimes|string|max:100',
            'telefono' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:150|unique:tecnicos,email,' . $tecnicoId,
            'especialidad' => 'sometimes|string|max:100',
            'activo' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'cedula.unique' => 'La cédula ya está registrada',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'El email ya está registrado',
        ];
    }
}
