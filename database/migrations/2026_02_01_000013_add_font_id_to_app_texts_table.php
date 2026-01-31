<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_texts', function (Blueprint $table) {
            $table->foreignId('font_id')
                ->nullable()
                ->constrained('fonts')
                ->nullOnDelete()
                ->after('locale');
        });
    }

    public function down(): void
    {
        Schema::table('app_texts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('font_id');
        });
    }
};
