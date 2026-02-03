<?php

declare(strict_types=1);

namespace Src\OrdenReparacion\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrdenReparacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('tecnicoId') || $this->has('tecnico_id')) {
            $data['tecnico_id'] = $this->tecnicoId ?? $this->tecnico_id;
        }
        if ($this->has('problemaReportado') || $this->has('problema_reportado')) {
            $data['problema_reportado'] = $this->problemaReportado ?? $this->problema_reportado;
        }
        if ($this->has('diagnostico')) {
            $data['diagnostico'] = $this->diagnostico;
        }
        if ($this->has('solucionAplicada') || $this->has('solucion_aplicada')) {
            $data['solucion_aplicada'] = $this->solucionAplicada ?? $this->solucion_aplicada;
        }
        if ($this->has('estado')) {
            $data['estado'] = $this->estado;
        }
        if ($this->has('costoEstimado') || $this->has('costo_estimado')) {
            $data['costo_estimado'] = $this->costoEstimado ?? $this->costo_estimado;
        }
        if ($this->has('costoFinal') || $this->has('costo_final')) {
            $data['costo_final'] = $this->costoFinal ?? $this->costo_final;
        }
        if ($this->has('observaciones')) {
            $data['observaciones'] = $this->observaciones;
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        return [
            'tecnico_id' => 'nullable|uuid|exists:tecnicos,id',
            'problema_reportado' => 'sometimes|string',
            'diagnostico' => 'nullable|string',
            'solucion_aplicada' => 'nullable|string',
            'estado' => 'sometimes|in:RECIBIDO,EN_DIAGNOSTICO,PENDIENTE_AUTORIZACION,AUTORIZADO,EN_REPARACION,REPARADO,ENTREGADO,CANCELADO',
            'costo_estimado' => 'nullable|numeric|min:0',
            'costo_final' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'tecnico_id.exists' => 'El técnico seleccionado no existe',
            'estado.in' => 'El estado seleccionado no es válido',
            'costo_estimado.numeric' => 'El costo estimado debe ser numérico',
            'costo_final.numeric' => 'El costo final debe ser numérico',
        ];
    }
}
