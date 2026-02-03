<?php

use Illuminate\Support\Facades\Route;
use Src\OrdenReparacion\Application\Controllers\OrdenReparacionWebController;

Route::middleware(['auth'])->prefix('ordenes-reparacion')->group(function () {
    Route::get('/', [OrdenReparacionWebController::class, 'index'])->name('ordenes-reparacion.index');
    Route::get('/pendientes', [OrdenReparacionWebController::class, 'pendientes'])->name('ordenes-reparacion.pendientes');
    Route::get('/create', [OrdenReparacionWebController::class, 'create'])->name('ordenes-reparacion.create');
    Route::get('/{id}', [OrdenReparacionWebController::class, 'show'])->name('ordenes-reparacion.show');
    Route::get('/{id}/edit', [OrdenReparacionWebController::class, 'edit'])->name('ordenes-reparacion.edit');
});
