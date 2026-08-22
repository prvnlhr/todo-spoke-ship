<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        if (config('app.role') === 'hub') {
            return app(DashboardController::class)();
        }

        return app(TodoController::class)->index($request);
    }
}
