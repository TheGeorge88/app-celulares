<?php

declare(strict_types=1);

namespace Src\ConsultaEstado\Application\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ConsultaEstadoWebController extends Controller
{
    /**
     * Página pública para consultar estado de reparación
     */
    public function index(): Response
    {
        return Inertia::render('ConsultaEstado/Index');
    }

    /**
     * Página para mostrar resultado de consulta
     */
    public function resultado(string $codigoSeguimiento): Response
    {
        return Inertia::render('ConsultaEstado/Resultado', [
            'codigoSeguimiento' => $codigoSeguimiento,
        ]);
    }

    /**
     * Página para autorizar reparación
     */
    public function autorizar(string $codigoSeguimiento): Response
    {
        return Inertia::render('ConsultaEstado/Autorizar', [
            'codigoSeguimiento' => $codigoSeguimiento,
        ]);
    }

    /**
     * Página para ver historial del cliente
     */
    public function historial(): Response
    {
        return Inertia::render('ConsultaEstado/Historial');
    }
}
