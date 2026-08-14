<?php

namespace App\Console\Commands;

use App\Models\Todo;
use App\Models\UserMenu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SyncHub extends Command
{
    protected $signature = 'sync:hub';

    protected $description = 'Push local todos and pull hub menus (spoke only)';

    public function handle(): int
    {
        if (config('app.role') !== 'spoke') {
            $this->error('sync:hub runs only when APP_ROLE=spoke');

            return self::FAILURE;
        }

        if (! config('app.sync_enabled')) {
            $this->warn('Sync disabled (SYNC_ENABLED=false).');

            return self::SUCCESS;
        }

        $hubUrl = rtrim((string) config('app.hub_url'), '/');
        $token = config('app.hub_api_token');

        if (! $hubUrl || ! $token) {
            $this->error('Set HUB_URL and HUB_API_TOKEN in .env');

            return self::FAILURE;
        }

        $this->info("Syncing with {$hubUrl}...");

        if (! $this->pushTodos($hubUrl, $token)) {
            return self::FAILURE;
        }

        if (! $this->pullMenus($hubUrl, $token)) {
            return self::FAILURE;
        }

        $this->info('Sync complete.');

        return self::SUCCESS;
    }

    private function pushTodos(string $hubUrl, string $token): bool
    {
        $spokeId = config('app.spoke_id');

        $todos = Todo::withTrashed()
            ->where('spoke_id', $spokeId)
            ->where(function ($q) {
                $q->whereNull('synced_at')
                    ->orWhereColumn('updated_at', '>', 'synced_at');
            })
            ->get();

        if ($todos->isEmpty()) {
            $this->line('No todos to push.');

            return true;
        }

        $payload = [
            'todos' => $todos->map(fn (Todo $t) => [
                'id' => $t->id,
                'title' => $t->title,
                'done' => $t->done,
                'updated_at' => $t->updated_at?->toIso8601String(),
                'deleted_at' => $t->deleted_at?->toIso8601String(),
            ])->values()->all(),
        ];

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(30)
            ->post("{$hubUrl}/api/sync/push", $payload);

        if (! $response->successful()) {
            $this->error('Push failed: '.$response->body());

            return false;
        }

        Todo::query()
            ->whereIn('id', $todos->pluck('id'))
            ->update(['synced_at' => now()]);

        $this->line("Pushed {$todos->count()} todo(s).");

        return true;
    }

    private function pullMenus(string $hubUrl, string $token): bool
    {
        $since = Cache::get('sync.menus.cursor');

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(30)
            ->get("{$hubUrl}/api/sync/pull", array_filter(['since' => $since]));

        if (! $response->successful()) {
            $this->error('Pull failed: '.$response->body());

            return false;
        }

        $data = $response->json();
        $menus = $data['menus'] ?? [];

        foreach ($menus as $row) {
            $menu = UserMenu::withTrashed()->updateOrCreate(
                ['id' => $row['id']],
                [
                    'label' => $row['label'],
                    'href' => $row['href'],
                    'icon' => $row['icon'] ?? null,
                    'spoke_id' => $row['spoke_id'],
                    'sort_order' => $row['sort_order'] ?? 0,
                    'updated_at' => $row['updated_at'],
                ]
            );

            if (! empty($row['deleted_at'])) {
                $menu->delete();
            } else {
                $menu->restore();
            }
        }

        if (! empty($data['cursor'])) {
            Cache::forever('sync.menus.cursor', $data['cursor']);
        }

        $this->line('Pulled '.count($menus).' menu row(s).');

        if (! empty($data['app_version'])) {
            $local = config('app.version');
            $remote = $data['app_version'];
            if ($remote !== $local) {
                $this->warn("Hub app version {$remote} differs from spoke {$local}. Pull a new spoke image to update UI.");
            }
        }

        return true;
    }
}
