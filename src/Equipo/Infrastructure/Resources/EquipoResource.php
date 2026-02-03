<?php

declare(strict_types=1);

namespace Src\Equipo\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Cliente\Infrastructure\Resources\ClienteResource;

class EquipoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'clienteId' => $this->cliente_id,
            'cliente' => $this->whenLoaded('cliente', fn() => new ClienteResource($this->cliente)),
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'imei' => $this->imei,
            'color' => $this->color,
            'descripcionCompleta' => "{$this->marca} {$this->modelo} - {$this->color}",
            'observaciones' => $this->observaciones,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
