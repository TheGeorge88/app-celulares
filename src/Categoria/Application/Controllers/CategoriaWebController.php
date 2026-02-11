<?php

declare(strict_types=1);

namespace Src\Categoria\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Src\Categoria\Infrastructure\Models\CategoriaEloquentModel;
use Src\Categoria\Infrastructure\Requests\StoreCategoriaRequest;
use Src\Categoria\Infrastructure\Requests\UpdateCategoriaRequest;
use Src\Categoria\Infrastructure\Resources\CategoriaResource;
use Exception;

class CategoriaWebController extends Controller
{
    public function index(): Response
    {
        $categorias = CategoriaEloquentModel::orderBy('nombre')->get();

        return Inertia::render('Categorias/Index', [
            'categorias' => CategoriaResource::collection($categorias),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Categorias/Create');
    }

    public function store(StoreCategoriaRequest $request): RedirectResponse
    {
        try {
            CategoriaEloquentModel::create($request->validated());

            return redirect()
                ->route('categorias.index')
                ->with('success', 'Categoría registrada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al registrar la categoría: ' . $e->getMessage());
        }
    }

    public function edit(string $id): Response
    {
        $categoria = CategoriaEloquentModel::findOrFail($id);

        return Inertia::render('Categorias/Edit', [
            'categoria' => new CategoriaResource($categoria),
        ]);
    }

    public function update(UpdateCategoriaRequest $request, string $id): RedirectResponse
    {
        try {
            $categoria = CategoriaEloquentModel::findOrFail($id);
            $categoria->update($request->validated());

            return redirect()
                ->route('categorias.index')
                ->with('success', 'Categoría actualizada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la categoría: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $categoria = CategoriaEloquentModel::find($id);

        if (!$categoria) {
            return redirect()->back()->with('error', 'Categoría no encontrada');
        }

        if ($categoria->productos()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar esta categoría porque tiene productos asociados');
        }

        $categoria->delete();

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría eliminada exitosamente');
    }
}
