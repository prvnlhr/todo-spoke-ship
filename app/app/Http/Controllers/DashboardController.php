<?php

namespace App\Http\Controllers;

use App\Models\Spoke;
use App\Models\Todo;
use App\Models\UserMenu;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'spokeCount' => Spoke::query()->count(),
            'todoCount' => Todo::query()->count(),
            'menuCount' => UserMenu::query()->count(),
            'activeSpokes' => Spoke::query()->where('is_active', true)->count(),
            'recentTodos' => Todo::query()
                ->with('spoke')
                ->orderByDesc('updated_at')
                ->limit(8)
                ->get(),
        ]);
    }
}
