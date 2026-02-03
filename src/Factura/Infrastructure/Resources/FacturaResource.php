<?php

namespace Src\Factura\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Cliente\Infrastructure\Resources\ClienteResource;
use Src\Factura\Infrastructure\Resources\DetalleFacturaResource;
use Src\Auth\Infrastructure\Resources\UserResource;
use Src\OrdenReparacion\Infrastructure\Resources\OrdenReparacionResource;

class FacturaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numeroFactura' => $this->numero_factura,
            'serie' => $this->serie,
            'clienteId' => $this->cliente_id,
            'ordenReparacionId' => $this->orden_reparacion_id,
            'usuarioId' => $this->usuario_id,
            'fechaEmision' => $this->fecha_emision?->format('Y-m-d'),
            'fechaVencimiento' => $this->fecha_vencimiento?->format('Y-m-d'),
            'subtotal' => (float) $this->subtotal,
            'costoManoObra' => (float) ($this->costo_mano_obra ?? 0),
            'costoRepuestos' => (float) ($this->costo_repuestos ?? 0),
            'igv' => (float) $this->igv,
            'descuento' => (float) $this->descuento,
            'total' => (float) $this->total,
            'estado' => $this->estado,
            'metodoPago' => $this->metodo_pago,
            'observaciones' => $this->observaciones,
            'cliente' => $this->whenLoaded('cliente', function() {
                return [
                    'id' => $this->cliente->id,
                    'razonSocial' => $this->cliente->razon_social,
                    'numeroDocumento' => $this->cliente->numero_documento,
                    'email' => $this->cliente->email,
                    'telefono' => $this->cliente->telefono,
                ];
            }),
            'ordenReparacion' => $this->whenLoaded('ordenReparacion', function() {
                return [
                    'id' => $this->ordenReparacion->id,
                    'codigoSeguimiento' => $this->ordenReparacion->codigo_seguimiento,
                    'estado' => $this->ordenReparacion->estado,
                ];
            }),
            'usuario' => $this->whenLoaded('usuario', function() {
                return [
                    'id' => $this->usuario->id,
                    'name' => $this->usuario->name,
                    'email' => $this->usuario->email,
                ];
            }),
            'detalles' => $this->whenLoaded('detalles', function() {
                return DetalleFacturaResource::collection($this->detalles);
            }),
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
