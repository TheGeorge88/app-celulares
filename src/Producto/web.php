<?php

use Illuminate\Support\Facades\Route;
use Src\Producto\Application\Controllers\ProductoWebController;

Route::middleware(['auth'])->prefix('productos')->group(function () {
    Route::get('/', [ProductoWebController::class, 'index'])->name('productos.index');
    Route::get('/create', [ProductoWebController::class, 'create'])->name('productos.create');
    Route::get('/{id}/edit', [ProductoWebController::class, 'edit'])->name('productos.edit');
});
