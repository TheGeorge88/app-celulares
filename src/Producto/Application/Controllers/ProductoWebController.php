<?php

declare(strict_types=1);

namespace Src\Producto\Application\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\Producto\Infrastructure\Models\ProductoEloquentModel;
use Src\Producto\Infrastructure\Resources\ProductoResource;
use Src\Categoria\Infrastructure\Models\CategoriaEloquentModel;
use Src\Categoria\Infrastructure\Resources\CategoriaResource;

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

    public function edit(string $id): Response
    {
        $producto = ProductoEloquentModel::with('categoria')->findOrFail($id);
        $categorias = CategoriaEloquentModel::where('activo', true)->orderBy('nombre')->get();

        return Inertia::render('Productos/Edit', [
            'producto' => new ProductoResource($producto),
            'categorias' => CategoriaResource::collection($categorias),
        ]);
    }
}
