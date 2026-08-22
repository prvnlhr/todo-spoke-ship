<?php

namespace Database\Seeders;

use App\Models\Spoke;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (config('app.role') !== 'hub') {
            return;
        }

        Spoke::query()->updateOrCreate(
            ['id' => 'post-north-01'],
            ['name' => 'Border Post North']
        );

        Spoke::query()->updateOrCreate(
            ['id' => 'post-south-01'],
            ['name' => 'Border Post South']
        );
    }
}
