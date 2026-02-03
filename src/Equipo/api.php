<?php

use Illuminate\Support\Facades\Route;
use Src\Equipo\Application\Controllers\EquipoController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('equipos/cliente/{clienteId}', [EquipoController::class, 'porCliente'])->name('api.equipos.por-cliente');

    Route::apiResource('equipos', EquipoController::class)->names([
        'index' => 'api.equipos.index',
        'store' => 'api.equipos.store',
        'show' => 'api.equipos.show',
        'update' => 'api.equipos.update',
        'destroy' => 'api.equipos.destroy',
    ]);
});
