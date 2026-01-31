<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_texts', function (Blueprint $table) {
            $table->string('font_size', 32)->nullable()->after('font_id');
            $table->string('text_color', 16)->nullable()->after('font_size');
        });
    }

    public function down(): void
    {
        Schema::table('app_texts', function (Blueprint $table) {
            $table->dropColumn(['font_size', 'text_color']);
        });
    }
};
