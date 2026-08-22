<?php

namespace App\Http\Controllers;

use App\Models\Spoke;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpokeController extends Controller
{
    public function index(): View
    {
        $spokes = Spoke::query()
            ->withCount('todos')
            ->orderBy('name')
            ->get();

        return view('hub.spokes.index', ['spokes' => $spokes]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9\-]+$/', 'unique:spokes,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        Spoke::query()->create([
            'id' => strtolower(trim($data['id'])),
            'name' => trim($data['name']),
        ]);

        return redirect()
            ->route('spokes.index')
            ->with('status', 'Spoke added.');
    }

    public function show(Spoke $spoke): View
    {
        $todos = Todo::query()
            ->where('spoke_id', $spoke->id)
            ->orderBy('done')
            ->orderByDesc('updated_at')
            ->get();

        return view('hub.spokes.show', [
            'spoke' => $spoke,
            'todos' => $todos,
        ]);
    }

    public function destroy(Spoke $spoke): RedirectResponse
    {
        Todo::query()->where('spoke_id', $spoke->id)->delete();
        $spoke->delete();

        return redirect()
            ->route('spokes.index')
            ->with('status', 'Spoke removed.');
    }
}
