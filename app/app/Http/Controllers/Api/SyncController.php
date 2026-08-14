<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Spoke;
use App\Models\Todo;
use App\Models\UserMenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function push(Request $request): JsonResponse
    {
        /** @var Spoke $spoke */
        $spoke = $request->attributes->get('spoke');

        $data = $request->validate([
            'todos' => ['required', 'array'],
            'todos.*.id' => ['required', 'uuid'],
            'todos.*.title' => ['required', 'string', 'max:255'],
            'todos.*.done' => ['required', 'boolean'],
            'todos.*.updated_at' => ['required', 'date'],
            'todos.*.deleted_at' => ['nullable', 'date'],
        ]);

        foreach ($data['todos'] as $row) {
            $todo = Todo::withTrashed()->updateOrCreate(
                ['id' => $row['id']],
                [
                    'title' => $row['title'],
                    'done' => $row['done'],
                    'spoke_id' => $spoke->id,
                    'synced_at' => now(),
                    'updated_at' => $row['updated_at'],
                ]
            );

            if (! empty($row['deleted_at'])) {
                $todo->delete();
            } else {
                $todo->restore();
            }
        }

        $spoke->forceFill(['last_synced_at' => now()])->save();

        return response()->json(['ok' => true, 'received' => count($data['todos'])]);
    }

    public function pull(Request $request): JsonResponse
    {
        /** @var Spoke $spoke */
        $spoke = $request->attributes->get('spoke');

        $since = $request->query('since');

        $menus = UserMenu::withTrashed()
            ->forSpoke($spoke->id)
            ->when($since, fn ($q) => $q->where('updated_at', '>', $since))
            ->orderBy('sort_order')
            ->get(['id', 'label', 'href', 'icon', 'spoke_id', 'sort_order', 'updated_at', 'deleted_at']);

        $cursor = $menus->max('updated_at') ?? now()->toIso8601String();

        return response()->json([
            'cursor' => $cursor,
            'app_version' => config('app.version'),
            'menus' => $menus,
        ]);
    }
}
