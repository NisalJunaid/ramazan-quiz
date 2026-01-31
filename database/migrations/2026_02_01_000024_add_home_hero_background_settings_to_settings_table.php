<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('home_hero_background_image')->nullable();
            $table->decimal('home_hero_background_opacity', 3, 2)->default(0.15);
            $table->string('home_hero_background_fit')->default('cover');
            $table->string('home_hero_background_position')->default('center');
            $table->string('home_hero_background_repeat')->default('no-repeat');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_hero_background_image',
                'home_hero_background_opacity',
                'home_hero_background_fit',
                'home_hero_background_position',
                'home_hero_background_repeat',
            ]);
        });
    }
};
