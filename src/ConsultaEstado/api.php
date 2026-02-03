<?php

use Illuminate\Support\Facades\Route;
use Src\ConsultaEstado\Application\Controllers\ConsultaEstadoController;

// Rutas PÚBLICAS - No requieren autenticación
Route::prefix('consulta-estado')->group(function () {
    Route::post('/consultar', [ConsultaEstadoController::class, 'consultar'])
        ->name('api.consulta-estado.consultar');

    Route::post('/autorizar', [ConsultaEstadoController::class, 'autorizar'])
        ->name('api.consulta-estado.autorizar');

    Route::post('/historial', [ConsultaEstadoController::class, 'historialCliente'])
        ->name('api.consulta-estado.historial');
});
