<?php

use Illuminate\Support\Facades\Route;
use Src\OrdenReparacion\Application\Controllers\OrdenReparacionController;

Route::middleware('auth:sanctum')->group(function () {
    // Rutas adicionales
    Route::get('ordenes-reparacion/pendientes', [OrdenReparacionController::class, 'pendientes'])
        ->name('api.ordenes-reparacion.pendientes');
    Route::get('ordenes-reparacion/cliente/{clienteId}', [OrdenReparacionController::class, 'porCliente'])
        ->name('api.ordenes-reparacion.por-cliente');
    Route::get('ordenes-reparacion/tecnico/{tecnicoId}', [OrdenReparacionController::class, 'porTecnico'])
        ->name('api.ordenes-reparacion.por-tecnico');
    Route::patch('ordenes-reparacion/{id}/estado', [OrdenReparacionController::class, 'cambiarEstado'])
        ->name('api.ordenes-reparacion.cambiar-estado');
    Route::patch('ordenes-reparacion/{id}/asignar-tecnico', [OrdenReparacionController::class, 'asignarTecnico'])
        ->name('api.ordenes-reparacion.asignar-tecnico');
    Route::patch('ordenes-reparacion/{id}/diagnostico', [OrdenReparacionController::class, 'registrarDiagnostico'])
        ->name('api.ordenes-reparacion.registrar-diagnostico');

    // CRUD estándar
    Route::apiResource('ordenes-reparacion', OrdenReparacionController::class)->names([
        'index' => 'api.ordenes-reparacion.index',
        'store' => 'api.ordenes-reparacion.store',
        'show' => 'api.ordenes-reparacion.show',
        'update' => 'api.ordenes-reparacion.update',
        'destroy' => 'api.ordenes-reparacion.destroy',
    ]);
});
