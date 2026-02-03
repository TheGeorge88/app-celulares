<?php

declare(strict_types=1);

namespace Src\Tecnico\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Tecnico\Infrastructure\Models\TecnicoEloquentModel;
use Src\Tecnico\Infrastructure\Requests\StoreTecnicoRequest;
use Src\Tecnico\Infrastructure\Requests\UpdateTecnicoRequest;
use Src\Tecnico\Infrastructure\Resources\TecnicoResource;

class TecnicoController extends Controller
{
    public function index(): JsonResponse
    {
        $tecnicos = TecnicoEloquentModel::orderBy('nombre')->get();
        return response()->json(TecnicoResource::collection($tecnicos));
    }

    public function store(StoreTecnicoRequest $request): JsonResponse
    {
        $tecnico = TecnicoEloquentModel::create($request->validated());
        return response()->json(new TecnicoResource($tecnico), 201);
    }

    public function show(string $id): JsonResponse
    {
        $tecnico = TecnicoEloquentModel::find($id);

        if (!$tecnico) {
            return response()->json([
                'message' => 'Técnico no encontrado'
            ], 404);
        }

        return response()->json(new TecnicoResource($tecnico));
    }

    public function update(UpdateTecnicoRequest $request, string $id): JsonResponse
    {
        $tecnico = TecnicoEloquentModel::find($id);

        if (!$tecnico) {
            return response()->json([
                'message' => 'Técnico no encontrado'
            ], 404);
        }

        $tecnico->update($request->validated());

        return response()->json(new TecnicoResource($tecnico));
    }

    public function destroy(string $id): JsonResponse
    {
        $tecnico = TecnicoEloquentModel::find($id);

        if (!$tecnico) {
            return response()->json([
                'message' => 'Técnico no encontrado'
            ], 404);
        }

        // Verificar si tiene órdenes de reparación asignadas
        if ($tecnico->ordenesReparacion()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el técnico porque tiene órdenes de reparación asignadas'
            ], 400);
        }

        $tecnico->delete();

        return response()->json([
            'message' => 'Técnico eliminado correctamente'
        ]);
    }

    public function activos(): JsonResponse
    {
        $tecnicos = TecnicoEloquentModel::where('activo', true)->orderBy('nombre')->get();
        return response()->json(TecnicoResource::collection($tecnicos));
    }
}
