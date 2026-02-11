<?php

namespace Src\Cliente\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\Auth\Infrastructure\Models\UserEloquentModel;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Src\Cliente\Infrastructure\Resources\ClienteResource;
use Src\Cliente\Infrastructure\Requests\StoreClienteRequest;
use Src\Cliente\Infrastructure\Requests\UpdateClienteRequest;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Exception;

class ClienteWebController extends Controller
{
    public function index(): Response
    {
        $clientes = ClienteEloquentModel::with('user')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Cliente/index', [
            'customers' => [
                'data' => ClienteResource::collection($clientes),
                'links' => [],
                'meta' => [
                    'total' => $clientes->count(),
                    'per_page' => $clientes->count(),
                    'current_page' => 1,
                ]
            ],
            'stats' => [
                'total' => $clientes->count(),
                'active' => $clientes->count(),
                'inactive' => 0,
            ],
        ]);
    }

    public function create(): Response
    {
        $usuariosAsignados = ClienteEloquentModel::pluck('user_id')->toArray();
        $usuarios = UserEloquentModel::whereNotIn('id', $usuariosAsignados)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('Cliente/create', [
            'usuarios' => $usuarios,
        ]);
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        try {
            ClienteEloquentModel::create($request->validated());

            return redirect()
                ->route('clientes.index')
                ->with('success', 'Cliente creado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el cliente: ' . $e->getMessage());
        }
    }

    public function edit(string $id): Response
    {
        $cliente = ClienteEloquentModel::with('user')->findOrFail($id);

        $usuariosAsignados = ClienteEloquentModel::where('id', '!=', $id)
            ->pluck('user_id')
            ->toArray();
        $usuarios = UserEloquentModel::whereNotIn('id', $usuariosAsignados)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('Cliente/edit', [
            'cliente' => new ClienteResource($cliente),
            'usuarios' => $usuarios,
        ]);
    }

    public function update(UpdateClienteRequest $request, string $id): RedirectResponse
    {
        try {
            $cliente = ClienteEloquentModel::findOrFail($id);
            $cliente->update($request->validated());

            return redirect()
                ->route('clientes.index')
                ->with('success', 'Cliente actualizado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $cliente = ClienteEloquentModel::find($id);

        if (!$cliente) {
            return redirect()
                ->back()
                ->with('error', 'Cliente no encontrado');
        }

        if ($cliente->facturas()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'No se puede eliminar este cliente porque tiene facturas asociadas');
        }

        $cliente->delete();

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente');
    }
}
