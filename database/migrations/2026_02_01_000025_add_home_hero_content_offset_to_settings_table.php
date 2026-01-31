<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->integer('home_hero_content_offset')->nullable();
            $table->string('home_hero_content_offset_unit')->default('px');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->dropColumn([
                'home_hero_content_offset',
                'home_hero_content_offset_unit',
            ]);
        });
    }
};
