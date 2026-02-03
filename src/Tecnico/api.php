<?php

use Illuminate\Support\Facades\Route;
use Src\Tecnico\Application\Controllers\TecnicoController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('tecnicos/activos', [TecnicoController::class, 'activos'])->name('api.tecnicos.activos');

    Route::apiResource('tecnicos', TecnicoController::class)->names([
        'index' => 'api.tecnicos.index',
        'store' => 'api.tecnicos.store',
        'show' => 'api.tecnicos.show',
        'update' => 'api.tecnicos.update',
        'destroy' => 'api.tecnicos.destroy',
    ]);
});
