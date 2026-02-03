<?php

declare(strict_types=1);

namespace Src\Repuesto\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepuestoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'stock' => $this->stock,
            'stockMinimo' => $this->stock_minimo,
            'stockBajo' => $this->tieneStockBajo(),
            'precioCompra' => (float) $this->precio_compra,
            'precioVenta' => (float) $this->precio_venta,
            'activo' => $this->activo,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
