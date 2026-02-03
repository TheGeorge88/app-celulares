<?php

declare(strict_types=1);

namespace Src\DetalleOrdenRepuesto\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\DetalleOrdenRepuesto\Infrastructure\Models\DetalleOrdenRepuestoEloquentModel;
use Src\DetalleOrdenRepuesto\Infrastructure\Resources\DetalleOrdenRepuestoResource;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;

class DetalleOrdenRepuestoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'ordenReparacionId' => 'required|uuid|exists:ordenes_reparacion,id',
            'repuestoId' => 'required|uuid|exists:repuestos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $repuesto = RepuestoEloquentModel::find($request->repuestoId);

        if (!$repuesto->hayDisponibilidad($request->cantidad)) {
            return response()->json([
                'message' => 'No hay suficiente stock disponible del repuesto'
            ], 400);
        }

        // Crear el detalle
        $detalle = DetalleOrdenRepuestoEloquentModel::create([
            'orden_reparacion_id' => $request->ordenReparacionId,
            'repuesto_id' => $request->repuestoId,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $repuesto->precio_venta,
        ]);

        // Descontar stock
        $repuesto->descontarStock($request->cantidad);

        $detalle->load('repuesto');

        return response()->json(new DetalleOrdenRepuestoResource($detalle), 201);
    }

    public function destroy(string $id): JsonResponse
    {
        $detalle = DetalleOrdenRepuestoEloquentModel::find($id);

        if (!$detalle) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }

        // Devolver stock
        $repuesto = RepuestoEloquentModel::find($detalle->repuesto_id);
        if ($repuesto) {
            $repuesto->agregarStock($detalle->cantidad);
        }

        $detalle->delete();

        return response()->json([
            'message' => 'Repuesto removido de la orden correctamente'
        ]);
    }

    public function porOrden(string $ordenId): JsonResponse
    {
        $detalles = DetalleOrdenRepuestoEloquentModel::where('orden_reparacion_id', $ordenId)
            ->with('repuesto')
            ->get();

        return response()->json(DetalleOrdenRepuestoResource::collection($detalles));
    }
}
