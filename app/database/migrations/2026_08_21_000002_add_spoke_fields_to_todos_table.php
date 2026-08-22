<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            if (! Schema::hasColumn('todos', 'spoke_id')) {
                $table->string('spoke_id')->nullable()->index()->after('done');
            }
            if (! Schema::hasColumn('todos', 'remote_id')) {
                $table->string('remote_id')->nullable()->index()->after('spoke_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            if (Schema::hasColumn('todos', 'remote_id')) {
                $table->dropColumn('remote_id');
            }
            if (Schema::hasColumn('todos', 'spoke_id')) {
                $table->dropColumn('spoke_id');
            }
        });
    }
};
