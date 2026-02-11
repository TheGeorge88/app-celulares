<?php

declare(strict_types=1);

namespace Src\Tecnico\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTecnicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->userId ?? $this->user_id,
            'especialidad' => $this->especialidad,
            'certificacion' => $this->certificacion,
            'fecha_contratacion' => $this->fechaContratacion ?? $this->fecha_contratacion,
            'activo' => $this->activo ?? true,
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|uuid|exists:users,id|unique:tecnicos,user_id',
            'especialidad' => 'required|string|max:100',
            'certificacion' => 'nullable|string|max:100',
            'fecha_contratacion' => 'nullable|date',
            'activo' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario es obligatorio',
            'user_id.exists' => 'El usuario seleccionado no existe',
            'user_id.unique' => 'Este usuario ya tiene un técnico asignado',
            'especialidad.required' => 'La especialidad es obligatoria',
        ];
    }
}
