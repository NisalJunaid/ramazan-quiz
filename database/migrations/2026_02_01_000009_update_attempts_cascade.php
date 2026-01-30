<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attempts', function (Blueprint $table) {
            $table->dropForeign(['quiz_day_id']);
            $table->foreign('quiz_day_id')
                ->references('id')
                ->on('quiz_days')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attempts', function (Blueprint $table) {
            $table->dropForeign(['quiz_day_id']);
            $table->foreign('quiz_day_id')
                ->references('id')
                ->on('quiz_days');
        });
    }
};
