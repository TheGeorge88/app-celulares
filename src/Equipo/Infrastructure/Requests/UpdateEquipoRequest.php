<?php

declare(strict_types=1);

namespace Src\Equipo\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('clienteId') || $this->has('cliente_id')) {
            $data['cliente_id'] = $this->clienteId ?? $this->cliente_id;
        }
        if ($this->has('marca')) $data['marca'] = $this->marca;
        if ($this->has('modelo')) $data['modelo'] = $this->modelo;
        if ($this->has('imei')) $data['imei'] = $this->imei;
        if ($this->has('color')) $data['color'] = $this->color;
        if ($this->has('observaciones')) $data['observaciones'] = $this->observaciones;

        $this->merge($data);
    }

    public function rules(): array
    {
        $equipoId = $this->route('id') ?? $this->route('equipo');

        return [
            'cliente_id' => 'sometimes|uuid|exists:clientes,id',
            'marca' => 'sometimes|string|max:100',
            'modelo' => 'sometimes|string|max:100',
            'imei' => 'sometimes|string|max:20|unique:equipos,imei,' . $equipoId,
            'color' => 'sometimes|string|max:50',
            'observaciones' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'El cliente seleccionado no existe',
            'imei.unique' => 'El IMEI ya está registrado',
        ];
    }
}
