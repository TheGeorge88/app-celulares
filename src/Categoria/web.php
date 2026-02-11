<?php

use Illuminate\Support\Facades\Route;
use Src\Categoria\Application\Controllers\CategoriaWebController;

Route::middleware(['auth'])->prefix('categorias')->group(function () {
    Route::get('/', [CategoriaWebController::class, 'index'])->name('categorias.index');
    Route::get('/create', [CategoriaWebController::class, 'create'])->name('categorias.create');
    Route::post('/', [CategoriaWebController::class, 'store'])->name('categorias.store');
    Route::get('/{id}/edit', [CategoriaWebController::class, 'edit'])->name('categorias.edit');
    Route::put('/{id}', [CategoriaWebController::class, 'update'])->name('categorias.update');
    Route::delete('/{id}', [CategoriaWebController::class, 'destroy'])->name('categorias.destroy');
});
