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

        if ($this->has('userId') || $this->has('user_id')) {
            $data['user_id'] = $this->userId ?? $this->user_id;
        }
        if ($this->has('especialidad')) $data['especialidad'] = $this->especialidad;
        if ($this->has('certificacion')) $data['certificacion'] = $this->certificacion;
        if ($this->has('fechaContratacion') || $this->has('fecha_contratacion')) {
            $data['fecha_contratacion'] = $this->fechaContratacion ?? $this->fecha_contratacion;
        }
        if ($this->has('activo')) $data['activo'] = $this->activo;

        $this->merge($data);
    }

    public function rules(): array
    {
        $tecnicoId = $this->route('id') ?? $this->route('tecnico');

        return [
            'user_id' => 'sometimes|uuid|exists:users,id|unique:tecnicos,user_id,' . $tecnicoId,
            'especialidad' => 'sometimes|string|max:100',
            'certificacion' => 'nullable|string|max:100',
            'fecha_contratacion' => 'nullable|date',
            'activo' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'El usuario seleccionado no existe',
            'user_id.unique' => 'Este usuario ya tiene un técnico asignado',
        ];
    }
}
