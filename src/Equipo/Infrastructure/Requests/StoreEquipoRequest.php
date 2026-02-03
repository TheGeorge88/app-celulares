<?php

declare(strict_types=1);

namespace Src\Equipo\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cliente_id' => $this->clienteId ?? $this->cliente_id,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'imei' => $this->imei,
            'color' => $this->color,
            'observaciones' => $this->observaciones,
        ]);
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|uuid|exists:clientes,id',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'imei' => 'required|string|max:20|unique:equipos,imei',
            'color' => 'required|string|max:50',
            'observaciones' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'El cliente es obligatorio',
            'cliente_id.exists' => 'El cliente seleccionado no existe',
            'marca.required' => 'La marca es obligatoria',
            'modelo.required' => 'El modelo es obligatorio',
            'imei.required' => 'El IMEI es obligatorio',
            'imei.unique' => 'El IMEI ya está registrado',
            'color.required' => 'El color es obligatorio',
        ];
    }
}
