<?php

declare(strict_types=1);

namespace Src\OrdenReparacion\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Cliente\Infrastructure\Resources\ClienteResource;
use Src\Equipo\Infrastructure\Resources\EquipoResource;
use Src\Tecnico\Infrastructure\Resources\TecnicoResource;

class OrdenReparacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigoSeguimiento' => $this->codigo_seguimiento,
            'clienteId' => $this->cliente_id,
            'cliente' => $this->whenLoaded('cliente', fn() => new ClienteResource($this->cliente)),
            'equipoId' => $this->equipo_id,
            'equipo' => $this->whenLoaded('equipo', fn() => new EquipoResource($this->equipo)),
            'tecnicoId' => $this->tecnico_id,
            'tecnico' => $this->whenLoaded('tecnico', fn() => $this->tecnico ? new TecnicoResource($this->tecnico) : null),
            'problemaReportado' => $this->problema_reportado,
            'diagnostico' => $this->diagnostico,
            'solucionAplicada' => $this->solucion_aplicada,
            'estado' => $this->estado,
            'estadoDescripcion' => $this->getEstadoDescripcion(),
            'costoEstimado' => $this->costo_estimado ? (float) $this->costo_estimado : null,
            'costoFinal' => $this->costo_final ? (float) $this->costo_final : null,
            'autorizado' => $this->autorizado,
            'fechaAutorizacion' => $this->fecha_autorizacion?->format('Y-m-d H:i:s'),
            'fechaEntrega' => $this->fecha_entrega?->format('Y-m-d H:i:s'),
            'observaciones' => $this->observaciones,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function getEstadoDescripcion(): string
    {
        return match($this->estado) {
            'RECIBIDO' => 'Recibido',
            'EN_DIAGNOSTICO' => 'En diagnóstico',
            'PENDIENTE_AUTORIZACION' => 'Pendiente de autorización',
            'AUTORIZADO' => 'Autorizado',
            'EN_REPARACION' => 'En reparación',
            'REPARADO' => 'Reparado',
            'ENTREGADO' => 'Entregado',
            'CANCELADO' => 'Cancelado',
            default => $this->estado,
        };
    }
}
