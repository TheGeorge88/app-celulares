<?php

declare(strict_types=1);

namespace Src\Equipo\Application\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Src\Cliente\Infrastructure\Resources\ClienteResource;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;
use Src\Equipo\Infrastructure\Resources\EquipoResource;

class EquipoWebController extends Controller
{
    public function index(): Response
    {
        $equipos = EquipoEloquentModel::with('cliente')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Equipos/Index', [
            'equipos' => EquipoResource::collection($equipos),
        ]);
    }

    public function create(): Response
    {
        $clientes = ClienteEloquentModel::orderBy('razon_social')->get();

        return Inertia::render('Equipos/Create', [
            'clientes' => ClienteResource::collection($clientes),
        ]);
    }

    public function show(string $id): Response
    {
        $equipo = EquipoEloquentModel::with('cliente')->findOrFail($id);

        return Inertia::render('Equipos/Show', [
            'equipo' => new EquipoResource($equipo),
        ]);
    }

    public function edit(string $id): Response
    {
        $equipo = EquipoEloquentModel::with('cliente')->findOrFail($id);
        $clientes = ClienteEloquentModel::orderBy('razon_social')->get();

        return Inertia::render('Equipos/Edit', [
            'equipo' => new EquipoResource($equipo),
            'clientes' => ClienteResource::collection($clientes),
        ]);
    }
}
