<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_menus')) {
            return;
        }

        Schema::create('user_menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('label');
            $table->string('href');
            $table->string('icon')->nullable();
            $table->string('spoke_id')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_menus');
    }
};
