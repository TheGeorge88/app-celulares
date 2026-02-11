<?php

declare(strict_types=1);

namespace Src\OrdenReparacion\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Src\Cliente\Infrastructure\Resources\ClienteResource;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;
use Src\Equipo\Infrastructure\Resources\EquipoResource;
use Src\OrdenReparacion\Infrastructure\Models\OrdenReparacionEloquentModel;
use Src\OrdenReparacion\Infrastructure\Requests\StoreOrdenReparacionRequest;
use Src\OrdenReparacion\Infrastructure\Requests\UpdateOrdenReparacionRequest;
use Src\OrdenReparacion\Infrastructure\Resources\OrdenReparacionResource;
use Src\Tecnico\Infrastructure\Models\TecnicoEloquentModel;
use Src\Tecnico\Infrastructure\Resources\TecnicoResource;
use Exception;

class OrdenReparacionWebController extends Controller
{
    public function index(): Response
    {
        $ordenes = OrdenReparacionEloquentModel::with(['cliente.user', 'equipo', 'tecnico.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('OrdenesReparacion/Index', [
            'ordenes' => OrdenReparacionResource::collection($ordenes),
        ]);
    }

    public function create(): Response
    {
        $clientes = ClienteEloquentModel::with('user')->orderBy('razon_social')->get();
        $tecnicos = TecnicoEloquentModel::with('user')->where('activo', true)->get();

        return Inertia::render('OrdenesReparacion/Create', [
            'clientes' => ClienteResource::collection($clientes),
            'tecnicos' => TecnicoResource::collection($tecnicos),
        ]);
    }

    public function store(StoreOrdenReparacionRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['codigo_seguimiento'] = 'ORD-' . strtoupper(substr(md5((string) now()->timestamp), 0, 8));
            $data['estado'] = 'RECIBIDO';

            OrdenReparacionEloquentModel::create($data);

            return redirect()
                ->route('ordenes-reparacion.index')
                ->with('success', 'Orden de reparación creada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear la orden: ' . $e->getMessage());
        }
    }

    public function show(string $id): Response
    {
        $orden = OrdenReparacionEloquentModel::with(['cliente.user', 'equipo', 'tecnico.user', 'detallesRepuestos.repuesto'])
            ->findOrFail($id);

        return Inertia::render('OrdenesReparacion/Show', [
            'orden' => new OrdenReparacionResource($orden),
        ]);
    }

    public function edit(string $id): Response
    {
        $orden = OrdenReparacionEloquentModel::with(['cliente.user', 'equipo', 'tecnico.user'])->findOrFail($id);
        $tecnicos = TecnicoEloquentModel::with('user')->where('activo', true)->get();

        return Inertia::render('OrdenesReparacion/Edit', [
            'orden' => new OrdenReparacionResource($orden),
            'tecnicos' => TecnicoResource::collection($tecnicos),
        ]);
    }

    public function update(UpdateOrdenReparacionRequest $request, string $id): RedirectResponse
    {
        try {
            $orden = OrdenReparacionEloquentModel::findOrFail($id);
            $orden->update($request->validated());

            return redirect()
                ->route('ordenes-reparacion.index')
                ->with('success', 'Orden de reparación actualizada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la orden: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $orden = OrdenReparacionEloquentModel::find($id);

        if (!$orden) {
            return redirect()->back()->with('error', 'Orden no encontrada');
        }

        if (in_array($orden->estado, ['EN_REPARACION', 'REPARADO', 'ENTREGADO'])) {
            return redirect()->back()->with('error', 'No se puede eliminar una orden en estado ' . $orden->estado);
        }

        $orden->delete();

        return redirect()
            ->route('ordenes-reparacion.index')
            ->with('success', 'Orden de reparación eliminada exitosamente');
    }

    public function pendientes(): Response
    {
        $ordenes = OrdenReparacionEloquentModel::whereNotIn('estado', ['ENTREGADO', 'CANCELADO'])
            ->with(['cliente.user', 'equipo', 'tecnico.user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('OrdenesReparacion/Pendientes', [
            'ordenes' => OrdenReparacionResource::collection($ordenes),
        ]);
    }
}
