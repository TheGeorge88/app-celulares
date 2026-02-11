<?php

use Illuminate\Support\Facades\Route;
use Src\Repuesto\Application\Controllers\RepuestoWebController;

Route::middleware(['auth'])->prefix('repuestos')->group(function () {
    Route::get('/', [RepuestoWebController::class, 'index'])->name('repuestos.index');
    Route::get('/stock-bajo', [RepuestoWebController::class, 'stockBajo'])->name('repuestos.stock-bajo');
    Route::get('/create', [RepuestoWebController::class, 'create'])->name('repuestos.create');
    Route::post('/', [RepuestoWebController::class, 'store'])->name('repuestos.store');
    Route::get('/{id}', [RepuestoWebController::class, 'show'])->name('repuestos.show');
    Route::get('/{id}/edit', [RepuestoWebController::class, 'edit'])->name('repuestos.edit');
    Route::put('/{id}', [RepuestoWebController::class, 'update'])->name('repuestos.update');
    Route::delete('/{id}', [RepuestoWebController::class, 'destroy'])->name('repuestos.destroy');
});
