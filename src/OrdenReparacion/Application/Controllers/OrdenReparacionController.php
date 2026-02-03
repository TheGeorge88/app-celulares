<?php

declare(strict_types=1);

namespace Src\OrdenReparacion\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;
use Src\OrdenReparacion\Infrastructure\Requests\StoreOrdenReparacionRequest;
use Src\OrdenReparacion\Infrastructure\Requests\UpdateOrdenReparacionRequest;
use Src\OrdenReparacion\Infrastructure\Resources\OrdenReparacionResource;

class OrdenReparacionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = OrdenReparacionEloquentModel::with(['cliente', 'equipo', 'tecnico']);

        // Filtros opcionales
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('clienteId')) {
            $query->where('cliente_id', $request->clienteId);
        }

        if ($request->has('tecnicoId')) {
            $query->where('tecnico_id', $request->tecnicoId);
        }

        $ordenes = $query->orderBy('created_at', 'desc')->get();

        return response()->json(OrdenReparacionResource::collection($ordenes));
    }

    public function store(StoreOrdenReparacionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['estado'] = 'RECIBIDO';

        $orden = OrdenReparacionEloquentModel::create($data);
        $orden->load(['cliente', 'equipo', 'tecnico']);

        return response()->json(new OrdenReparacionResource($orden), 201);
    }

    public function show(string $id): JsonResponse
    {
        $orden = OrdenReparacionEloquentModel::with(['cliente', 'equipo', 'tecnico', 'detallesRepuestos.repuesto'])->find($id);

        if (!$orden) {
            return response()->json([
                'message' => 'Orden de reparación no encontrada'
            ], 404);
        }

        return response()->json(new OrdenReparacionResource($orden));
    }

    public function update(UpdateOrdenReparacionRequest $request, string $id): JsonResponse
    {
        $orden = OrdenReparacionEloquentModel::find($id);

        if (!$orden) {
            return response()->json([
                'message' => 'Orden de reparación no encontrada'
            ], 404);
        }

        $orden->update($request->validated());
        $orden->load(['cliente', 'equipo', 'tecnico']);

        return response()->json(new OrdenReparacionResource($orden));
    }

    public function destroy(string $id): JsonResponse
    {
        $orden = OrdenReparacionEloquentModel::find($id);

        if (!$orden) {
            return response()->json([
                'message' => 'Orden de reparación no encontrada'
            ], 404);
        }

        // Solo se puede eliminar si está en estado CANCELADO
        if ($orden->estado !== 'CANCELADO') {
            return response()->json([
                'message' => 'Solo se pueden eliminar órdenes canceladas'
            ], 400);
        }

        $orden->delete();

        return response()->json([
            'message' => 'Orden de reparación eliminada correctamente'
        ]);
    }

    public function cambiarEstado(Request $request, string $id): JsonResponse
    {
        $orden = OrdenReparacionEloquentModel::find($id);

        if (!$orden) {
            return response()->json([
                'message' => 'Orden de reparación no encontrada'
            ], 404);
        }

        $request->validate([
            'estado' => 'required|in:RECIBIDO,EN_DIAGNOSTICO,PENDIENTE_AUTORIZACION,AUTORIZADO,EN_REPARACION,REPARADO,ENTREGADO,CANCELADO'
        ]);

        $orden->estado = $request->estado;

        // Si el estado es ENTREGADO, registrar fecha de entrega
        if ($request->estado === 'ENTREGADO') {
            $orden->fecha_entrega = now();
        }

        $orden->save();
        $orden->load(['cliente', 'equipo', 'tecnico']);

        return response()->json(new OrdenReparacionResource($orden));
    }

    public function asignarTecnico(Request $request, string $id): JsonResponse
    {
        $orden = OrdenReparacionEloquentModel::find($id);

        if (!$orden) {
            return response()->json([
                'message' => 'Orden de reparación no encontrada'
            ], 404);
        }

        $request->validate([
            'tecnicoId' => 'required|uuid|exists:tecnicos,id'
        ]);

        $orden->tecnico_id = $request->tecnicoId;
        $orden->save();
        $orden->load(['cliente', 'equipo', 'tecnico']);

        return response()->json(new OrdenReparacionResource($orden));
    }

    public function registrarDiagnostico(Request $request, string $id): JsonResponse
    {
        $orden = OrdenReparacionEloquentModel::find($id);

        if (!$orden) {
            return response()->json([
                'message' => 'Orden de reparación no encontrada'
            ], 404);
        }

        $request->validate([
            'diagnostico' => 'required|string',
            'costoEstimado' => 'required|numeric|min:0'
        ]);

        $orden->diagnostico = $request->diagnostico;
        $orden->costo_estimado = $request->costoEstimado;
        $orden->estado = 'PENDIENTE_AUTORIZACION';
        $orden->save();
        $orden->load(['cliente', 'equipo', 'tecnico']);

        return response()->json(new OrdenReparacionResource($orden));
    }

    public function porCliente(string $clienteId): JsonResponse
    {
        $ordenes = OrdenReparacionEloquentModel::where('cliente_id', $clienteId)
            ->with(['equipo', 'tecnico'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(OrdenReparacionResource::collection($ordenes));
    }

    public function porTecnico(string $tecnicoId): JsonResponse
    {
        $ordenes = OrdenReparacionEloquentModel::where('tecnico_id', $tecnicoId)
            ->with(['cliente', 'equipo'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(OrdenReparacionResource::collection($ordenes));
    }

    public function pendientes(): JsonResponse
    {
        $ordenes = OrdenReparacionEloquentModel::whereNotIn('estado', ['ENTREGADO', 'CANCELADO'])
            ->with(['cliente', 'equipo', 'tecnico'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(OrdenReparacionResource::collection($ordenes));
    }
}
