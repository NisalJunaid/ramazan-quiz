<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_days', function (Blueprint $table) {
            $table->foreignId('quiz_range_id')
                ->after('id')
                ->constrained('quiz_ranges')
                ->cascadeOnDelete();
            $table->dropUnique('quiz_days_quiz_date_unique');
            $table->unique(['quiz_range_id', 'quiz_date']);
        });
    }

    public function down(): void
    {
        Schema::table('quiz_days', function (Blueprint $table) {
            $table->dropUnique(['quiz_range_id', 'quiz_date']);
            $table->unique('quiz_date');
            $table->dropForeign(['quiz_range_id']);
            $table->dropColumn('quiz_range_id');
        });
    }
};
