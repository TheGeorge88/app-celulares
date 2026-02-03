<?php

declare(strict_types=1);

namespace Src\DetalleOrdenRepuesto\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Repuesto\Infrastructure\Resources\RepuestoResource;

class DetalleOrdenRepuestoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ordenReparacionId' => $this->orden_reparacion_id,
            'repuestoId' => $this->repuesto_id,
            'repuesto' => $this->whenLoaded('repuesto', fn() => new RepuestoResource($this->repuesto)),
            'cantidad' => $this->cantidad,
            'precioUnitario' => (float) $this->precio_unitario,
            'subtotal' => (float) $this->subtotal,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
