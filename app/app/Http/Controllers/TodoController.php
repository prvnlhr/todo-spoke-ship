<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\UserMenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TodoController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $spokeId = config('app.spoke_id');

        $todos = Todo::query()
            ->when($spokeId, fn ($q) => $q->where('spoke_id', $spokeId))
            ->orderBy('done')
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'done', 'created_at', 'updated_at']);

        if ($request->wantsJson()) {
            return response()->json(['todos' => $todos]);
        }

        $menuItems = UserMenu::query()
            ->forSpoke($spokeId)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get(['id', 'label', 'href', 'icon']);

        return view('todos', [
            'todos' => $todos,
            'menuItems' => $menuItems,
            'spokeId' => $spokeId,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $todo = Todo::query()->create([
            'title' => trim($data['title']),
            'done' => false,
            'spoke_id' => config('app.spoke_id'),
            'synced_at' => null,
        ]);

        return response()->json(['todo' => $todo], 201);
    }

    public function update(Request $request, Todo $todo): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'done' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('title', $data)) {
            $data['title'] = trim($data['title']);
        }

        $todo->fill($data);
        $todo->synced_at = null;
        $todo->save();

        return response()->json(['todo' => $todo->fresh()]);
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $todo->synced_at = null;
        $todo->save();
        $todo->delete();

        return response()->json(['ok' => true]);
    }
}
