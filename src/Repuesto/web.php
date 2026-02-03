<?php

use Illuminate\Support\Facades\Route;
use Src\Repuesto\Application\Controllers\RepuestoWebController;

Route::middleware(['auth'])->prefix('repuestos')->group(function () {
    Route::get('/', [RepuestoWebController::class, 'index'])->name('repuestos.index');
    Route::get('/stock-bajo', [RepuestoWebController::class, 'stockBajo'])->name('repuestos.stock-bajo');
    Route::get('/create', [RepuestoWebController::class, 'create'])->name('repuestos.create');
    Route::get('/{id}', [RepuestoWebController::class, 'show'])->name('repuestos.show');
    Route::get('/{id}/edit', [RepuestoWebController::class, 'edit'])->name('repuestos.edit');
});
