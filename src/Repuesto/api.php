<?php

use Illuminate\Support\Facades\Route;
use Src\Repuesto\Application\Controllers\RepuestoController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('repuestos/activos', [RepuestoController::class, 'activos'])->name('api.repuestos.activos');
    Route::get('repuestos/stock-bajo', [RepuestoController::class, 'stockBajo'])->name('api.repuestos.stock-bajo');
    Route::get('repuestos/buscar', [RepuestoController::class, 'buscar'])->name('api.repuestos.buscar');
    Route::patch('repuestos/{id}/ajustar-stock', [RepuestoController::class, 'ajustarStock'])->name('api.repuestos.ajustar-stock');

    Route::apiResource('repuestos', RepuestoController::class)->names([
        'index' => 'api.repuestos.index',
        'store' => 'api.repuestos.store',
        'show' => 'api.repuestos.show',
        'update' => 'api.repuestos.update',
        'destroy' => 'api.repuestos.destroy',
    ]);
});
