<?php

declare(strict_types=1);

namespace Src\Producto\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Src\Producto\Infrastructure\Models\ProductoEloquentModel;
use Src\Producto\Infrastructure\Requests\StoreProductoRequest;
use Src\Producto\Infrastructure\Requests\UpdateProductoRequest;
use Src\Producto\Infrastructure\Resources\ProductoResource;
use Src\Categoria\Infrastructure\Models\CategoriaEloquentModel;
use Src\Categoria\Infrastructure\Resources\CategoriaResource;
use Exception;

class ProductoWebController extends Controller
{
    public function index(): Response
    {
        $productos = ProductoEloquentModel::with('categoria')->orderBy('nombre')->get();

        return Inertia::render('Productos/Index', [
            'productos' => ProductoResource::collection($productos),
        ]);
    }

    public function create(): Response
    {
        $categorias = CategoriaEloquentModel::where('activo', true)->orderBy('nombre')->get();

        return Inertia::render('Productos/Create', [
            'categorias' => CategoriaResource::collection($categorias),
        ]);
    }

    public function store(StoreProductoRequest $request): RedirectResponse
    {
        try {
            ProductoEloquentModel::create($request->validated());

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto registrado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al registrar el producto: ' . $e->getMessage());
        }
    }

    public function edit(string $id): Response
    {
        $producto = ProductoEloquentModel::with('categoria')->findOrFail($id);
        $categorias = CategoriaEloquentModel::where('activo', true)->orderBy('nombre')->get();

        return Inertia::render('Productos/Edit', [
            'producto' => new ProductoResource($producto),
            'categorias' => CategoriaResource::collection($categorias),
        ]);
    }

    public function update(UpdateProductoRequest $request, string $id): RedirectResponse
    {
        try {
            $producto = ProductoEloquentModel::findOrFail($id);
            $producto->update($request->validated());

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto actualizado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $producto = ProductoEloquentModel::find($id);

        if (!$producto) {
            return redirect()->back()->with('error', 'Producto no encontrado');
        }

        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
}
