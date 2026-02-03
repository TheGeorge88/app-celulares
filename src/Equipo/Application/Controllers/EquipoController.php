<?php

declare(strict_types=1);

namespace Src\Equipo\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;
use Src\Equipo\Infrastructure\Requests\StoreEquipoRequest;
use Src\Equipo\Infrastructure\Requests\UpdateEquipoRequest;
use Src\Equipo\Infrastructure\Resources\EquipoResource;

class EquipoController extends Controller
{
    public function index(): JsonResponse
    {
        $equipos = EquipoEloquentModel::with('cliente')->orderBy('created_at', 'desc')->get();
        return response()->json(EquipoResource::collection($equipos));
    }

    public function store(StoreEquipoRequest $request): JsonResponse
    {
        $equipo = EquipoEloquentModel::create($request->validated());
        $equipo->load('cliente');
        return response()->json(new EquipoResource($equipo), 201);
    }

    public function show(string $id): JsonResponse
    {
        $equipo = EquipoEloquentModel::with('cliente')->find($id);

        if (!$equipo) {
            return response()->json([
                'message' => 'Equipo no encontrado'
            ], 404);
        }

        return response()->json(new EquipoResource($equipo));
    }

    public function update(UpdateEquipoRequest $request, string $id): JsonResponse
    {
        $equipo = EquipoEloquentModel::find($id);

        if (!$equipo) {
            return response()->json([
                'message' => 'Equipo no encontrado'
            ], 404);
        }

        $equipo->update($request->validated());
        $equipo->load('cliente');

        return response()->json(new EquipoResource($equipo));
    }

    public function destroy(string $id): JsonResponse
    {
        $equipo = EquipoEloquentModel::find($id);

        if (!$equipo) {
            return response()->json([
                'message' => 'Equipo no encontrado'
            ], 404);
        }

        // Verificar si tiene órdenes de reparación
        if ($equipo->ordenesReparacion()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el equipo porque tiene órdenes de reparación asociadas'
            ], 400);
        }

        $equipo->delete();

        return response()->json([
            'message' => 'Equipo eliminado correctamente'
        ]);
    }

    public function porCliente(string $clienteId): JsonResponse
    {
        $equipos = EquipoEloquentModel::where('cliente_id', $clienteId)
            ->with('cliente')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(EquipoResource::collection($equipos));
    }
}
