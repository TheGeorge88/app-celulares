<?php

declare(strict_types=1);

namespace Src\Tecnico\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Tecnico\Infrastructure\Models\TecnicoEloquentModel;
use Src\Tecnico\Infrastructure\Resources\TecnicoResource;

class TecnicoWebController extends Controller
{
    public function index(): Response
    {
        $tecnicos = TecnicoEloquentModel::orderBy('nombre')->get();

        return Inertia::render('Tecnicos/Index', [
            'tecnicos' => TecnicoResource::collection($tecnicos),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tecnicos/Create');
    }

    public function show(string $id): Response
    {
        $tecnico = TecnicoEloquentModel::findOrFail($id);

        return Inertia::render('Tecnicos/Show', [
            'tecnico' => new TecnicoResource($tecnico),
        ]);
    }

    public function edit(string $id): Response
    {
        $tecnico = TecnicoEloquentModel::findOrFail($id);

        return Inertia::render('Tecnicos/Edit', [
            'tecnico' => new TecnicoResource($tecnico),
        ]);
    }
}
