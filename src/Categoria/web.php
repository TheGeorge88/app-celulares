<?php

use Illuminate\Support\Facades\Route;
use Src\Categoria\Application\Controllers\CategoriaWebController;

Route::middleware(['auth'])->prefix('categorias')->group(function () {
    Route::get('/', [CategoriaWebController::class, 'index'])->name('categorias.index');
    Route::get('/create', [CategoriaWebController::class, 'create'])->name('categorias.create');
    Route::get('/{id}/edit', [CategoriaWebController::class, 'edit'])->name('categorias.edit');
});
