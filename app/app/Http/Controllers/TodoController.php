<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TodoController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $todos = Todo::query()
            ->orderBy('done')
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'done', 'created_at', 'updated_at']);

        if ($request->wantsJson()) {
            return response()->json(['todos' => $todos]);
        }

        return view('todos', ['todos' => $todos]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $todo = Todo::query()->create([
            'title' => trim($data['title']),
            'done' => false,
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

        $todo->update($data);

        return response()->json(['todo' => $todo->fresh()]);
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();

        return response()->json(['ok' => true]);
    }
}
