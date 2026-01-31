<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('body_background_image')->nullable();
            $table->string('body_background_fit')->default('cover');
            $table->string('app_logo')->nullable();
            $table->decimal('logo_width', 8, 2)->nullable();
            $table->string('logo_width_unit', 10)->default('px');
            $table->decimal('logo_height', 8, 2)->nullable();
            $table->string('logo_height_unit', 10)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'body_background_image',
                'body_background_fit',
                'app_logo',
                'logo_width',
                'logo_width_unit',
                'logo_height',
                'logo_height_unit',
            ]);
        });
    }
};
