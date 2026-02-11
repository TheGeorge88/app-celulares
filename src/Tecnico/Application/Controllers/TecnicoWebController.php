<?php

declare(strict_types=1);

namespace Src\Tecnico\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Src\Auth\Infrastructure\Models\UserEloquentModel;
use Src\Tecnico\Infrastructure\Models\TecnicoEloquentModel;
use Src\Tecnico\Infrastructure\Requests\StoreTecnicoRequest;
use Src\Tecnico\Infrastructure\Requests\UpdateTecnicoRequest;
use Src\Tecnico\Infrastructure\Resources\TecnicoResource;
use Exception;

class TecnicoWebController extends Controller
{
    public function index(): Response
    {
        $tecnicos = TecnicoEloquentModel::with('user')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Tecnicos/Index', [
            'tecnicos' => TecnicoResource::collection($tecnicos),
        ]);
    }

    public function create(): Response
    {
        $usuariosAsignados = TecnicoEloquentModel::pluck('user_id')->toArray();
        $usuarios = UserEloquentModel::whereNotIn('id', $usuariosAsignados)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('Tecnicos/Create', [
            'usuarios' => $usuarios,
        ]);
    }

    public function store(StoreTecnicoRequest $request): RedirectResponse
    {
        try {
            TecnicoEloquentModel::create($request->validated());

            return redirect()
                ->route('tecnicos.index')
                ->with('success', 'Tecnico creado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el tecnico: ' . $e->getMessage());
        }
    }

    public function show(string $id): Response
    {
        $tecnico = TecnicoEloquentModel::with('user')->findOrFail($id);

        return Inertia::render('Tecnicos/Show', [
            'tecnico' => new TecnicoResource($tecnico),
        ]);
    }

    public function edit(string $id): Response
    {
        $tecnico = TecnicoEloquentModel::with('user')->findOrFail($id);

        $usuariosAsignados = TecnicoEloquentModel::where('id', '!=', $id)
            ->pluck('user_id')
            ->toArray();
        $usuarios = UserEloquentModel::whereNotIn('id', $usuariosAsignados)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('Tecnicos/Edit', [
            'tecnico' => new TecnicoResource($tecnico),
            'usuarios' => $usuarios,
        ]);
    }

    public function update(UpdateTecnicoRequest $request, string $id): RedirectResponse
    {
        try {
            $tecnico = TecnicoEloquentModel::findOrFail($id);
            $tecnico->update($request->validated());

            return redirect()
                ->route('tecnicos.index')
                ->with('success', 'Tecnico actualizado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el tecnico: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $tecnico = TecnicoEloquentModel::find($id);

        if (!$tecnico) {
            return redirect()
                ->back()
                ->with('error', 'Tecnico no encontrado');
        }

        if ($tecnico->ordenesReparacion()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'No se puede eliminar este tecnico porque tiene ordenes de reparacion asociadas');
        }

        $tecnico->delete();

        return redirect()
            ->route('tecnicos.index')
            ->with('success', 'Tecnico eliminado exitosamente');
    }
}
