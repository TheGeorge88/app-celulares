<?php

namespace Src\Factura\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetalleFacturaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'facturaId' => $this->factura_id,
            'tipo' => $this->tipo ?? 'producto',
            'concepto' => $this->concepto,
            'productoId' => $this->producto_id,
            'repuestoId' => $this->repuesto_id,
            'cantidad' => $this->cantidad,
            'precioUnitario' => (float) $this->precio_unitario,
            'descuento' => (float) $this->descuento,
            'subtotal' => (float) $this->subtotal,
            'factura' => $this->whenLoaded('factura', function() {
                return [
                    'id' => $this->factura->id,
                    'numeroFactura' => $this->factura->numero_factura,
                ];
            }),
            'producto' => $this->whenLoaded('producto', function() {
                if (!$this->producto) return null;
                return [
                    'id' => $this->producto->id,
                    'codigo' => $this->producto->codigo,
                    'nombre' => $this->producto->nombre,
                ];
            }),
            'repuesto' => $this->whenLoaded('repuesto', function() {
                if (!$this->repuesto) return null;
                return [
                    'id' => $this->repuesto->id,
                    'codigo' => $this->repuesto->codigo,
                    'nombre' => $this->repuesto->nombre,
                    'marca' => $this->repuesto->marca,
                ];
            }),
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
