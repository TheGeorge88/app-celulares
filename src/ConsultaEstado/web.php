<?php

use Illuminate\Support\Facades\Route;
use Src\ConsultaEstado\Application\Controllers\ConsultaEstadoWebController;

// Rutas PÚBLICAS - No requieren autenticación
Route::prefix('consulta')->group(function () {
    Route::get('/', [ConsultaEstadoWebController::class, 'index'])->name('consulta-estado.index');
    Route::get('/resultado/{codigoSeguimiento}', [ConsultaEstadoWebController::class, 'resultado'])
        ->name('consulta-estado.resultado');
    Route::get('/autorizar/{codigoSeguimiento}', [ConsultaEstadoWebController::class, 'autorizar'])
        ->name('consulta-estado.autorizar');
    Route::get('/historial', [ConsultaEstadoWebController::class, 'historial'])->name('consulta-estado.historial');
});
