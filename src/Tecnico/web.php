<?php

use Illuminate\Support\Facades\Route;
use Src\Tecnico\Application\Controllers\TecnicoWebController;

Route::middleware(['auth'])->prefix('tecnicos')->group(function () {
    Route::get('/', [TecnicoWebController::class, 'index'])->name('tecnicos.index');
    Route::get('/create', [TecnicoWebController::class, 'create'])->name('tecnicos.create');
    Route::post('/', [TecnicoWebController::class, 'store'])->name('tecnicos.store');
    Route::get('/{id}', [TecnicoWebController::class, 'show'])->name('tecnicos.show');
    Route::get('/{id}/edit', [TecnicoWebController::class, 'edit'])->name('tecnicos.edit');
    Route::put('/{id}', [TecnicoWebController::class, 'update'])->name('tecnicos.update');
    Route::delete('/{id}', [TecnicoWebController::class, 'destroy'])->name('tecnicos.destroy');
});
