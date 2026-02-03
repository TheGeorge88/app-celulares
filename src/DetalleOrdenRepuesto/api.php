<?php

use Illuminate\Support\Facades\Route;
use Src\DetalleOrdenRepuesto\Application\Controllers\DetalleOrdenRepuestoController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('detalles-orden-repuesto/orden/{ordenId}', [DetalleOrdenRepuestoController::class, 'porOrden'])
        ->name('api.detalles-orden-repuesto.por-orden');
    Route::post('detalles-orden-repuesto', [DetalleOrdenRepuestoController::class, 'store'])
        ->name('api.detalles-orden-repuesto.store');
    Route::delete('detalles-orden-repuesto/{id}', [DetalleOrdenRepuestoController::class, 'destroy'])
        ->name('api.detalles-orden-repuesto.destroy');
});
