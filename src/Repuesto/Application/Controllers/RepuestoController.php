<?php

declare(strict_types=1);

namespace Src\Repuesto\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;
use Src\Repuesto\Infrastructure\Requests\StoreRepuestoRequest;
use Src\Repuesto\Infrastructure\Requests\UpdateRepuestoRequest;
use Src\Repuesto\Infrastructure\Resources\RepuestoResource;

class RepuestoController extends Controller
{
    public function index(): JsonResponse
    {
        $repuestos = RepuestoEloquentModel::orderBy('nombre')->get();
        return response()->json(RepuestoResource::collection($repuestos));
    }

    public function store(StoreRepuestoRequest $request): JsonResponse
    {
        $repuesto = RepuestoEloquentModel::create($request->validated());
        return response()->json(new RepuestoResource($repuesto), 201);
    }

    public function show(string $id): JsonResponse
    {
        $repuesto = RepuestoEloquentModel::find($id);

        if (!$repuesto) {
            return response()->json([
                'message' => 'Repuesto no encontrado'
            ], 404);
        }

        return response()->json(new RepuestoResource($repuesto));
    }

    public function update(UpdateRepuestoRequest $request, string $id): JsonResponse
    {
        $repuesto = RepuestoEloquentModel::find($id);

        if (!$repuesto) {
            return response()->json([
                'message' => 'Repuesto no encontrado'
            ], 404);
        }

        $repuesto->update($request->validated());

        return response()->json(new RepuestoResource($repuesto));
    }

    public function destroy(string $id): JsonResponse
    {
        $repuesto = RepuestoEloquentModel::find($id);

        if (!$repuesto) {
            return response()->json([
                'message' => 'Repuesto no encontrado'
            ], 404);
        }

        // Verificar si está siendo usado en órdenes
        if ($repuesto->detallesOrden()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el repuesto porque está siendo usado en órdenes de reparación'
            ], 400);
        }

        $repuesto->delete();

        return response()->json([
            'message' => 'Repuesto eliminado correctamente'
        ]);
    }

    public function activos(): JsonResponse
    {
        $repuestos = RepuestoEloquentModel::where('activo', true)
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get();

        return response()->json(RepuestoResource::collection($repuestos));
    }

    public function stockBajo(): JsonResponse
    {
        $repuestos = RepuestoEloquentModel::whereColumn('stock', '<=', 'stock_minimo')
            ->orderBy('stock')
            ->get();

        return response()->json(RepuestoResource::collection($repuestos));
    }

    public function ajustarStock(Request $request, string $id): JsonResponse
    {
        $repuesto = RepuestoEloquentModel::find($id);

        if (!$repuesto) {
            return response()->json([
                'message' => 'Repuesto no encontrado'
            ], 404);
        }

        $request->validate([
            'cantidad' => 'required|integer',
            'tipo' => 'required|in:entrada,salida'
        ]);

        if ($request->tipo === 'entrada') {
            $repuesto->agregarStock($request->cantidad);
        } else {
            if (!$repuesto->hayDisponibilidad($request->cantidad)) {
                return response()->json([
                    'message' => 'No hay suficiente stock disponible'
                ], 400);
            }
            $repuesto->descontarStock($request->cantidad);
        }

        return response()->json(new RepuestoResource($repuesto));
    }

    public function buscar(Request $request): JsonResponse
    {
        $query = RepuestoEloquentModel::query();

        if ($request->has('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ilike', "%{$search}%")
                    ->orWhere('codigo', 'ilike', "%{$search}%")
                    ->orWhere('marca', 'ilike', "%{$search}%")
                    ->orWhere('modelo', 'ilike', "%{$search}%");
            });
        }

        if ($request->has('marca')) {
            $query->where('marca', $request->marca);
        }

        if ($request->has('modelo')) {
            $query->where('modelo', $request->modelo);
        }

        $repuestos = $query->where('activo', true)->orderBy('nombre')->get();

        return response()->json(RepuestoResource::collection($repuestos));
    }
}
