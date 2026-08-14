<?php

use App\Http\Controllers\Api\SyncController;
use App\Http\Middleware\AuthenticateSpoke;
use App\Http\Middleware\EnsureHubRole;
use Illuminate\Support\Facades\Route;

Route::middleware([EnsureHubRole::class, AuthenticateSpoke::class])
    ->prefix('sync')
    ->group(function () {
        Route::post('/push', [SyncController::class, 'push']);
        Route::get('/pull', [SyncController::class, 'pull']);
    });
