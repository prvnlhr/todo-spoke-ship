<?php

namespace App\Http\Controllers;

use App\Models\Spoke;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function create(): View
    {
        return view('hub.import', [
            'spokes' => Spoke::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'spoke_id' => ['required', 'string', 'exists:spokes,id'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $raw = file_get_contents($data['file']->getRealPath());
        $payload = json_decode($raw, true);

        if (! is_array($payload)) {
            return back()->withErrors(['file' => 'Invalid JSON file.']);
        }

        $spokeId = $data['spoke_id'];
        $fileSpokeId = $payload['spoke_id'] ?? null;
        if ($fileSpokeId && $fileSpokeId !== $spokeId) {
            return back()->withErrors([
                'file' => "File is for spoke \"{$fileSpokeId}\", but you selected \"{$spokeId}\".",
            ]);
        }

        $todos = $payload['todos'] ?? null;
        if (! is_array($todos)) {
            return back()->withErrors(['file' => 'JSON must include a "todos" array.']);
        }

        $imported = 0;

        DB::transaction(function () use ($spokeId, $todos, &$imported) {
            foreach ($todos as $row) {
                if (! is_array($row) || empty($row['id']) || ! isset($row['title'])) {
                    continue;
                }

                $remoteId = (string) $row['id'];
                $title = trim((string) $row['title']);
                if ($title === '') {
                    continue;
                }

                Todo::query()->updateOrCreate(
                    [
                        'spoke_id' => $spokeId,
                        'remote_id' => $remoteId,
                    ],
                    [
                        'title' => $title,
                        'done' => (bool) ($row['done'] ?? false),
                        'updated_at' => $row['updated_at'] ?? now(),
                    ]
                );
                $imported++;
            }

            Spoke::query()->whereKey($spokeId)->update([
                'last_imported_at' => now(),
            ]);
        });

        return redirect()
            ->route('spokes.show', $spokeId)
            ->with('status', "Imported {$imported} todo(s) for this spoke.");
    }
}
