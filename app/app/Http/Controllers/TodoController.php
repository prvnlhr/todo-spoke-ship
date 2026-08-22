<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TodoController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $spokeId = config('app.spoke_id');

        $todos = Todo::query()
            ->when($spokeId, fn ($q) => $q->where(function ($q) use ($spokeId) {
                $q->where('spoke_id', $spokeId)->orWhereNull('spoke_id');
            }))
            ->orderBy('done')
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'done', 'created_at', 'updated_at']);

        if ($request->wantsJson()) {
            return response()->json(['todos' => $todos]);
        }

        return view('todos', [
            'todos' => $todos,
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

    public function export(): StreamedResponse|JsonResponse
    {
        $spokeId = config('app.spoke_id') ?: 'spoke-local';

        $todos = Todo::query()
            ->when(config('app.spoke_id'), fn ($q) => $q->where(function ($q) {
                $spokeId = config('app.spoke_id');
                $q->where('spoke_id', $spokeId)->orWhereNull('spoke_id');
            }))
            ->orderBy('id')
            ->get(['id', 'title', 'done', 'updated_at']);

        $payload = [
            'spoke_id' => $spokeId,
            'exported_at' => now()->toIso8601String(),
            'todos' => $todos->map(fn (Todo $t) => [
                'id' => (string) $t->id,
                'title' => $t->title,
                'done' => $t->done,
                'updated_at' => $t->updated_at?->toIso8601String(),
            ])->values()->all(),
        ];

        $filename = $spokeId.'-'.now()->format('Ymd-His').'.json';

        return response()->streamDownload(function () use ($payload) {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }
}
