<?php

namespace Database\Seeders;

use App\Models\Spoke;
use App\Models\Todo;
use App\Models\UserMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (config('app.role') !== 'hub') {
            return;
        }

        $demo = Spoke::query()->updateOrCreate(
            ['id' => 'spoke-demo-01'],
            [
                'name' => 'Demo Spoke',
                'api_token' => 'token-demo-01',
                'is_active' => true,
            ]
        );

        $acme = Spoke::query()->updateOrCreate(
            ['id' => 'client-acme-01'],
            [
                'name' => 'Acme Clinic',
                'api_token' => 'token-acme-01',
                'is_active' => true,
            ]
        );

        if (Todo::query()->count() === 0) {
            Todo::query()->create([
                'id' => (string) Str::uuid(),
                'title' => 'Buy milk',
                'done' => false,
                'spoke_id' => $demo->id,
                'synced_at' => now(),
            ]);
            Todo::query()->create([
                'id' => (string) Str::uuid(),
                'title' => 'Call supplier',
                'done' => true,
                'spoke_id' => $demo->id,
                'synced_at' => now(),
            ]);
            Todo::query()->create([
                'id' => (string) Str::uuid(),
                'title' => 'Restock bandages',
                'done' => false,
                'spoke_id' => $acme->id,
                'synced_at' => now(),
            ]);
        }

        if (UserMenu::query()->count() === 0) {
            UserMenu::query()->create([
                'label' => 'Home',
                'href' => '/',
                'icon' => 'home',
                'spoke_id' => null,
                'sort_order' => 1,
            ]);
            UserMenu::query()->create([
                'label' => 'Acme Reports',
                'href' => '/reports',
                'icon' => 'chart',
                'spoke_id' => $acme->id,
                'sort_order' => 2,
            ]);
        }
    }
}
