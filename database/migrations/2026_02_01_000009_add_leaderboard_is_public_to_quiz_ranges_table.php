<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_ranges', function (Blueprint $table) {
            $table->boolean('leaderboard_is_public')->default(true)->after('is_visible');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_ranges', function (Blueprint $table) {
            $table->dropColumn('leaderboard_is_public');
        });
    }
};
