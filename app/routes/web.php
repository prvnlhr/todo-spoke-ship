<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\SpokeController;
use App\Http\Controllers\TodoController;
use App\Http\Middleware\EnsureHubRole;
use App\Http\Middleware\EnsureSpokeRole;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware(EnsureHubRole::class)->group(function () {
    Route::get('/spokes', [SpokeController::class, 'index'])->name('spokes.index');
    Route::post('/spokes', [SpokeController::class, 'store'])->name('spokes.store');
    Route::get('/spokes/{spoke}', [SpokeController::class, 'show'])->name('spokes.show');
    Route::delete('/spokes/{spoke}', [SpokeController::class, 'destroy'])->name('spokes.destroy');
    Route::get('/import', [ImportController::class, 'create'])->name('import.create');
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');
});

Route::middleware(EnsureSpokeRole::class)->group(function () {
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::patch('/todos/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
    Route::get('/export', [TodoController::class, 'export'])->name('todos.export');
});
