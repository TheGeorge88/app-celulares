<?php

declare(strict_types=1);

namespace Src\Equipo\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Src\Cliente\Infrastructure\Resources\ClienteResource;
use Src\Equipo\Infrastructure\Models\EquipoEloquentModel;
use Src\Equipo\Infrastructure\Requests\StoreEquipoRequest;
use Src\Equipo\Infrastructure\Requests\UpdateEquipoRequest;
use Src\Equipo\Infrastructure\Resources\EquipoResource;
use Exception;

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

    public function store(StoreEquipoRequest $request): RedirectResponse
    {
        try {
            EquipoEloquentModel::create($request->validated());

            return redirect()
                ->route('equipos.index')
                ->with('success', 'Equipo registrado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al registrar el equipo: ' . $e->getMessage());
        }
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

    public function update(UpdateEquipoRequest $request, string $id): RedirectResponse
    {
        try {
            $equipo = EquipoEloquentModel::findOrFail($id);
            $equipo->update($request->validated());

            return redirect()
                ->route('equipos.index')
                ->with('success', 'Equipo actualizado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el equipo: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $equipo = EquipoEloquentModel::find($id);

        if (!$equipo) {
            return redirect()->back()->with('error', 'Equipo no encontrado');
        }

        if ($equipo->ordenesReparacion()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar este equipo porque tiene órdenes de reparación asociadas');
        }

        $equipo->delete();

        return redirect()
            ->route('equipos.index')
            ->with('success', 'Equipo eliminado exitosamente');
    }
}
