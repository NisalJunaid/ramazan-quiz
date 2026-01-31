<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('home_logo_width', 8, 2)->nullable();
            $table->string('home_logo_width_unit', 10)->default('px');
            $table->decimal('home_logo_height', 8, 2)->nullable();
            $table->string('home_logo_height_unit', 10)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_logo_width',
                'home_logo_width_unit',
                'home_logo_height',
                'home_logo_height_unit',
            ]);
        });
    }
};
