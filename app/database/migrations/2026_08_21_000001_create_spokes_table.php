<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('spokes')) {
            return;
        }

        Schema::create('spokes', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->timestamp('last_imported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spokes');
    }
};
