<?php

namespace App\Http\Controllers;

use App\Models\Spoke;
use Illuminate\View\View;

class SpokeController extends Controller
{
    public function index(): View
    {
        $spokes = Spoke::query()
            ->withCount('todos')
            ->orderBy('name')
            ->get();

        return view('spokes.index', compact('spokes'));
    }

    public function show(Spoke $spoke): View
    {
        $todos = $spoke->todos()
            ->orderBy('done')
            ->orderByDesc('updated_at')
            ->get();

        return view('spokes.show', compact('spoke', 'todos'));
    }
}
