<?php

declare(strict_types=1);

namespace Src\Tecnico\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TecnicoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'usuario' => $this->user?->name,
            'email' => $this->user?->email,
            'especialidad' => $this->especialidad,
            'certificacion' => $this->certificacion,
            'fechaContratacion' => $this->fecha_contratacion?->format('Y-m-d'),
            'activo' => $this->activo,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
