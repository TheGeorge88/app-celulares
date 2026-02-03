<?php

declare(strict_types=1);

namespace Src\ConsultaEstado\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;

class ConsultaEstadoController extends Controller
{
    /**
     * Consultar el estado de una orden de reparación por código de seguimiento
     * Este endpoint es PÚBLICO - no requiere autenticación
     */
    public function consultar(Request $request): JsonResponse
    {
        $request->validate([
            'codigoSeguimiento' => 'required|string'
        ]);

        $orden = OrdenReparacionEloquentModel::where('codigo_seguimiento', $request->codigoSeguimiento)
            ->with(['equipo', 'tecnico', 'detallesRepuestos.repuesto'])
            ->first();

        if (!$orden) {
            return response()->json([
                'message' => 'No se encontró ninguna orden con el código de seguimiento proporcionado',
                'encontrado' => false
            ], 404);
        }

        return response()->json([
            'encontrado' => true,
            'orden' => [
                'codigoSeguimiento' => $orden->codigo_seguimiento,
                'estado' => $orden->estado,
                'estadoDescripcion' => $this->getEstadoDescripcion($orden->estado),
                'estadoColor' => $this->getEstadoColor($orden->estado),
                'equipo' => [
                    'marca' => $orden->equipo->marca,
                    'modelo' => $orden->equipo->modelo,
                    'color' => $orden->equipo->color,
                ],
                'problemaReportado' => $orden->problema_reportado,
                'diagnostico' => $orden->diagnostico,
                'costoEstimado' => $orden->costo_estimado ? (float) $orden->costo_estimado : null,
                'costoFinal' => $orden->costo_final ? (float) $orden->costo_final : null,
                'autorizado' => $orden->autorizado,
                'fechaAutorizacion' => $orden->fecha_autorizacion?->format('Y-m-d H:i:s'),
                'fechaRecepcion' => $orden->created_at->format('Y-m-d H:i:s'),
                'fechaEntrega' => $orden->fecha_entrega?->format('Y-m-d H:i:s'),
                'tecnicoAsignado' => $orden->tecnico ? $orden->tecnico->nombre . ' ' . $orden->tecnico->apellido : null,
                'requiereAutorizacion' => $orden->estado === 'PENDIENTE_AUTORIZACION' && !$orden->autorizado,
                'repuestosUtilizados' => $orden->detallesRepuestos->map(function ($detalle) {
                    return [
                        'nombre' => $detalle->repuesto->nombre,
                        'cantidad' => $detalle->cantidad,
                        'precioUnitario' => (float) $detalle->precio_unitario,
                        'subtotal' => (float) $detalle->subtotal,
                    ];
                }),
                'observaciones' => $orden->observaciones,
            ],
            'timeline' => $this->generarTimeline($orden),
        ]);
    }

    /**
     * Autorizar la reparación de un equipo
     * Este endpoint es PÚBLICO - usa código de seguimiento + número de documento del cliente
     */
    public function autorizar(Request $request): JsonResponse
    {
        $request->validate([
            'codigoSeguimiento' => 'required|string',
            'numeroDocumento' => 'required|string',
            'autorizar' => 'required|boolean',
            'observacionesCliente' => 'nullable|string'
        ]);

        $orden = OrdenReparacionEloquentModel::where('codigo_seguimiento', $request->codigoSeguimiento)
            ->with(['cliente', 'equipo'])
            ->first();

        if (!$orden) {
            return response()->json([
                'message' => 'No se encontró ninguna orden con el código de seguimiento proporcionado',
                'success' => false
            ], 404);
        }

        // Verificar que el número de documento coincide con el del cliente
        if ($orden->cliente->numero_documento !== $request->numeroDocumento) {
            return response()->json([
                'message' => 'El número de documento no coincide con el registrado para esta orden',
                'success' => false
            ], 403);
        }

        // Verificar que la orden está en estado pendiente de autorización
        if ($orden->estado !== 'PENDIENTE_AUTORIZACION') {
            return response()->json([
                'message' => 'Esta orden no está pendiente de autorización',
                'estadoActual' => $orden->estado,
                'success' => false
            ], 400);
        }

        // Procesar la autorización
        if ($request->autorizar) {
            $orden->autorizado = true;
            $orden->fecha_autorizacion = now();
            $orden->estado = 'AUTORIZADO';
            $mensaje = 'La reparación ha sido autorizada exitosamente. Procederemos con la reparación de su equipo.';
        } else {
            $orden->autorizado = false;
            $orden->estado = 'CANCELADO';
            $mensaje = 'La reparación ha sido rechazada. Puede pasar a recoger su equipo.';
        }

        if ($request->observacionesCliente) {
            $orden->observaciones = ($orden->observaciones ? $orden->observaciones . "\n" : '') .
                "[CLIENTE]: " . $request->observacionesCliente;
        }

        $orden->save();

        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'orden' => [
                'codigoSeguimiento' => $orden->codigo_seguimiento,
                'estado' => $orden->estado,
                'autorizado' => $orden->autorizado,
                'fechaAutorizacion' => $orden->fecha_autorizacion?->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Obtener historial de órdenes por número de documento del cliente
     */
    public function historialCliente(Request $request): JsonResponse
    {
        $request->validate([
            'numeroDocumento' => 'required|string'
        ]);

        $ordenes = OrdenReparacionEloquentModel::whereHas('cliente', function ($query) use ($request) {
            $query->where('numero_documento', $request->numeroDocumento);
        })
            ->with(['equipo'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($ordenes->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron órdenes para el número de documento proporcionado',
                'ordenes' => []
            ]);
        }

        return response()->json([
            'ordenes' => $ordenes->map(function ($orden) {
                return [
                    'codigoSeguimiento' => $orden->codigo_seguimiento,
                    'equipo' => $orden->equipo->marca . ' ' . $orden->equipo->modelo,
                    'estado' => $orden->estado,
                    'estadoDescripcion' => $this->getEstadoDescripcion($orden->estado),
                    'estadoColor' => $this->getEstadoColor($orden->estado),
                    'fechaRecepcion' => $orden->created_at->format('Y-m-d'),
                    'fechaEntrega' => $orden->fecha_entrega?->format('Y-m-d'),
                    'costoFinal' => $orden->costo_final ? (float) $orden->costo_final : null,
                ];
            })
        ]);
    }

    private function getEstadoDescripcion(string $estado): string
    {
        return match ($estado) {
            'RECIBIDO' => 'Recibido - Su equipo ha sido recibido en nuestro taller',
            'EN_DIAGNOSTICO' => 'En diagnóstico - Nuestro técnico está evaluando su equipo',
            'PENDIENTE_AUTORIZACION' => 'Pendiente de autorización - Se requiere su aprobación para continuar',
            'AUTORIZADO' => 'Autorizado - La reparación ha sido aprobada',
            'EN_REPARACION' => 'En reparación - Su equipo está siendo reparado',
            'REPARADO' => 'Reparado - Su equipo está listo para ser recogido',
            'ENTREGADO' => 'Entregado - Su equipo ha sido entregado',
            'CANCELADO' => 'Cancelado - La orden ha sido cancelada',
            default => $estado,
        };
    }

    private function getEstadoColor(string $estado): string
    {
        return match ($estado) {
            'RECIBIDO' => 'blue',
            'EN_DIAGNOSTICO' => 'purple',
            'PENDIENTE_AUTORIZACION' => 'orange',
            'AUTORIZADO' => 'teal',
            'EN_REPARACION' => 'yellow',
            'REPARADO' => 'green',
            'ENTREGADO' => 'gray',
            'CANCELADO' => 'red',
            default => 'gray',
        };
    }

    private function generarTimeline($orden): array
    {
        $timeline = [];

        // Siempre mostrar recepción
        $timeline[] = [
            'estado' => 'RECIBIDO',
            'descripcion' => 'Equipo recibido',
            'fecha' => $orden->created_at->format('Y-m-d H:i'),
            'completado' => true,
        ];

        // Estados intermedios según el progreso
        $estadosOrden = [
            'EN_DIAGNOSTICO' => 'En diagnóstico',
            'PENDIENTE_AUTORIZACION' => 'Diagnóstico completado',
            'AUTORIZADO' => 'Autorizado por cliente',
            'EN_REPARACION' => 'En reparación',
            'REPARADO' => 'Reparación completada',
            'ENTREGADO' => 'Entregado al cliente',
        ];

        $estadoActual = $orden->estado;
        $estadosPasados = [];

        if ($estadoActual === 'CANCELADO') {
            $timeline[] = [
                'estado' => 'CANCELADO',
                'descripcion' => 'Orden cancelada',
                'fecha' => $orden->updated_at->format('Y-m-d H:i'),
                'completado' => true,
            ];
            return $timeline;
        }

        $posicionActual = array_search($estadoActual, array_keys($estadosOrden));

        foreach ($estadosOrden as $estado => $descripcion) {
            $posicion = array_search($estado, array_keys($estadosOrden));
            $completado = $posicion !== false && $posicionActual !== false && $posicion <= $posicionActual;

            $fecha = null;
            if ($estado === 'AUTORIZADO' && $orden->fecha_autorizacion) {
                $fecha = $orden->fecha_autorizacion->format('Y-m-d H:i');
            } elseif ($estado === 'ENTREGADO' && $orden->fecha_entrega) {
                $fecha = $orden->fecha_entrega->format('Y-m-d H:i');
            }

            $timeline[] = [
                'estado' => $estado,
                'descripcion' => $descripcion,
                'fecha' => $fecha,
                'completado' => $completado,
                'actual' => $estado === $estadoActual,
            ];
        }

        return $timeline;
    }
}
