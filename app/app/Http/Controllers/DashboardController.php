<?php

namespace App\Http\Controllers;

use App\Models\Spoke;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('hub.dashboard', [
            'spokeCount' => Spoke::query()->count(),
            'todoCount' => Todo::query()->whereNotNull('spoke_id')->count(),
            'recentSpokes' => Spoke::query()
                ->orderByDesc('last_imported_at')
                ->orderBy('name')
                ->limit(8)
                ->get(),
        ]);
    }
}
