<?php

declare(strict_types=1);

namespace Src\OrdenReparacion\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdenReparacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cliente_id' => $this->clienteId ?? $this->cliente_id,
            'equipo_id' => $this->equipoId ?? $this->equipo_id,
            'tecnico_id' => $this->tecnicoId ?? $this->tecnico_id,
            'problema_reportado' => $this->problemaReportado ?? $this->problema_reportado,
            'costo_estimado' => $this->costoEstimado ?? $this->costo_estimado,
            'observaciones' => $this->observaciones,
        ]);
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|uuid|exists:clientes,id',
            'equipo_id' => 'required|uuid|exists:equipos,id',
            'tecnico_id' => 'nullable|uuid|exists:tecnicos,id',
            'problema_reportado' => 'required|string',
            'costo_estimado' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'El cliente es obligatorio',
            'cliente_id.exists' => 'El cliente seleccionado no existe',
            'equipo_id.required' => 'El equipo es obligatorio',
            'equipo_id.exists' => 'El equipo seleccionado no existe',
            'tecnico_id.exists' => 'El técnico seleccionado no existe',
            'problema_reportado.required' => 'El problema reportado es obligatorio',
            'costo_estimado.numeric' => 'El costo estimado debe ser numérico',
            'costo_estimado.min' => 'El costo estimado no puede ser negativo',
        ];
    }
}
