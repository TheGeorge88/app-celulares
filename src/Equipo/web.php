<?php

use Illuminate\Support\Facades\Route;
use Src\Equipo\Application\Controllers\EquipoWebController;

Route::middleware(['auth'])->prefix('equipos')->group(function () {
    Route::get('/', [EquipoWebController::class, 'index'])->name('equipos.index');
    Route::get('/create', [EquipoWebController::class, 'create'])->name('equipos.create');
    Route::post('/', [EquipoWebController::class, 'store'])->name('equipos.store');
    Route::get('/{id}', [EquipoWebController::class, 'show'])->name('equipos.show');
    Route::get('/{id}/edit', [EquipoWebController::class, 'edit'])->name('equipos.edit');
    Route::put('/{id}', [EquipoWebController::class, 'update'])->name('equipos.update');
    Route::delete('/{id}', [EquipoWebController::class, 'destroy'])->name('equipos.destroy');
});
