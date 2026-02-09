<?php

declare(strict_types=1);

namespace Src\Categoria\Application\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\Categoria\Infrastructure\Models\CategoriaEloquentModel;
use Src\Categoria\Infrastructure\Resources\CategoriaResource;

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

    public function edit(string $id): Response
    {
        $categoria = CategoriaEloquentModel::findOrFail($id);

        return Inertia::render('Categorias/Edit', [
            'categoria' => new CategoriaResource($categoria),
        ]);
    }
}
