<?php

declare(strict_types=1);

namespace Src\Repuesto\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;
use Src\Repuesto\Infrastructure\Requests\StoreRepuestoRequest;
use Src\Repuesto\Infrastructure\Requests\UpdateRepuestoRequest;
use Src\Repuesto\Infrastructure\Resources\RepuestoResource;
use Exception;

class RepuestoWebController extends Controller
{
    public function index(): Response
    {
        $repuestos = RepuestoEloquentModel::orderBy('nombre')->get();

        return Inertia::render('Repuestos/Index', [
            'repuestos' => RepuestoResource::collection($repuestos),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Repuestos/Create');
    }

    public function store(StoreRepuestoRequest $request): RedirectResponse
    {
        try {
            RepuestoEloquentModel::create($request->validated());

            return redirect()
                ->route('repuestos.index')
                ->with('success', 'Repuesto registrado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al registrar el repuesto: ' . $e->getMessage());
        }
    }

    public function show(string $id): Response
    {
        $repuesto = RepuestoEloquentModel::findOrFail($id);

        return Inertia::render('Repuestos/Show', [
            'repuesto' => new RepuestoResource($repuesto),
        ]);
    }

    public function edit(string $id): Response
    {
        $repuesto = RepuestoEloquentModel::findOrFail($id);

        return Inertia::render('Repuestos/Edit', [
            'repuesto' => new RepuestoResource($repuesto),
        ]);
    }

    public function update(UpdateRepuestoRequest $request, string $id): RedirectResponse
    {
        try {
            $repuesto = RepuestoEloquentModel::findOrFail($id);
            $repuesto->update($request->validated());

            return redirect()
                ->route('repuestos.index')
                ->with('success', 'Repuesto actualizado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el repuesto: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $repuesto = RepuestoEloquentModel::find($id);

        if (!$repuesto) {
            return redirect()->back()->with('error', 'Repuesto no encontrado');
        }

        $repuesto->delete();

        return redirect()
            ->route('repuestos.index')
            ->with('success', 'Repuesto eliminado exitosamente');
    }

    public function stockBajo(): Response
    {
        $repuestos = RepuestoEloquentModel::whereColumn('stock', '<=', 'stock_minimo')
            ->orderBy('stock')
            ->get();

        return Inertia::render('Repuestos/StockBajo', [
            'repuestos' => RepuestoResource::collection($repuestos),
        ]);
    }
}
