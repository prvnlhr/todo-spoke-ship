<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SpokeController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserMenuController;
use App\Http\Middleware\EnsureHubRole;
use App\Http\Middleware\EnsureSpokeRole;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('dashboard');

Route::middleware(EnsureHubRole::class)->group(function () {
    Route::get('/spokes', [SpokeController::class, 'index'])->name('spokes.index');
    Route::get('/spokes/{spoke}', [SpokeController::class, 'show'])->name('spokes.show');
    Route::get('/menus', [UserMenuController::class, 'index'])->name('menus.index');
    Route::post('/menus', [UserMenuController::class, 'store'])->name('menus.store');
    Route::patch('/menus/{menu}', [UserMenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{menu}', [UserMenuController::class, 'destroy'])->name('menus.destroy');
});

Route::middleware(EnsureSpokeRole::class)->group(function () {
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::patch('/todos/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
});
