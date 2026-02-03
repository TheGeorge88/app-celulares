<?php

use Illuminate\Support\Facades\Route;
use Src\Equipo\Application\Controllers\EquipoWebController;

Route::middleware(['auth'])->prefix('equipos')->group(function () {
    Route::get('/', [EquipoWebController::class, 'index'])->name('equipos.index');
    Route::get('/create', [EquipoWebController::class, 'create'])->name('equipos.create');
    Route::get('/{id}', [EquipoWebController::class, 'show'])->name('equipos.show');
    Route::get('/{id}/edit', [EquipoWebController::class, 'edit'])->name('equipos.edit');
});
