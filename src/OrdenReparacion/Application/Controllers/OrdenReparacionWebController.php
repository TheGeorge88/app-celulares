<?php

declare(strict_types=1);

namespace Src\OrdenReparacion\Application\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Src\Cliente\Infrastructure\Resources\ClienteResource;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;
use Src\Equipo\Infrastructure\Resources\EquipoResource;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;
use Src\OrdenReparacion\Infrastructure\Resources\OrdenReparacionResource;
use Src\Tecnico\Infrastructure\Models\TecnicoEloquentModel;
use Src\Tecnico\Infrastructure\Resources\TecnicoResource;

class OrdenReparacionWebController extends Controller
{
    public function index(): Response
    {
        $ordenes = OrdenReparacionEloquentModel::with(['cliente', 'equipo', 'tecnico'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('OrdenesReparacion/Index', [
            'ordenes' => OrdenReparacionResource::collection($ordenes),
        ]);
    }

    public function create(): Response
    {
        $clientes = ClienteEloquentModel::orderBy('razon_social')->get();
        $tecnicos = TecnicoEloquentModel::where('activo', true)->orderBy('nombre')->get();

        return Inertia::render('OrdenesReparacion/Create', [
            'clientes' => ClienteResource::collection($clientes),
            'tecnicos' => TecnicoResource::collection($tecnicos),
        ]);
    }

    public function show(string $id): Response
    {
        $orden = OrdenReparacionEloquentModel::with(['cliente', 'equipo', 'tecnico', 'detallesRepuestos.repuesto'])
            ->findOrFail($id);

        return Inertia::render('OrdenesReparacion/Show', [
            'orden' => new OrdenReparacionResource($orden),
        ]);
    }

    public function edit(string $id): Response
    {
        $orden = OrdenReparacionEloquentModel::with(['cliente', 'equipo', 'tecnico'])->findOrFail($id);
        $tecnicos = TecnicoEloquentModel::where('activo', true)->orderBy('nombre')->get();

        return Inertia::render('OrdenesReparacion/Edit', [
            'orden' => new OrdenReparacionResource($orden),
            'tecnicos' => TecnicoResource::collection($tecnicos),
        ]);
    }

    public function pendientes(): Response
    {
        $ordenes = OrdenReparacionEloquentModel::whereNotIn('estado', ['ENTREGADO', 'CANCELADO'])
            ->with(['cliente', 'equipo', 'tecnico'])
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('OrdenesReparacion/Pendientes', [
            'ordenes' => OrdenReparacionResource::collection($ordenes),
        ]);
    }
}
