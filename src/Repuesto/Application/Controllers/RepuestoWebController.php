<?php

declare(strict_types=1);

namespace Src\Repuesto\Application\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\Repuesto\Infrastructure\Models\RepuestoEloquentModel;
use Src\Repuesto\Infrastructure\Resources\RepuestoResource;

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
