<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('primary_color')->nullable()->default('#059669');
            $table->string('primary_hover_color')->nullable()->default('#047857');
            $table->string('accent_color')->nullable()->default('#f59e0b');
            $table->string('surface_color')->nullable()->default('#ffffff');
            $table->string('surface_tint')->nullable()->default('rgba(255,255,255,0.90)');
            $table->string('text_color')->nullable()->default('#111827');
            $table->string('muted_text_color')->nullable()->default('#4b5563');
            $table->string('border_color')->nullable()->default('rgba(17,24,39,0.12)');
            $table->string('ring_color')->nullable()->default('rgba(5,150,105,0.18)');
            $table->string('button_radius')->nullable()->default('9999px');
            $table->string('card_radius')->nullable()->default('24px');
            $table->string('focus_ring_color')->nullable()->default('rgba(5,150,105,0.35)');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color',
                'primary_hover_color',
                'accent_color',
                'surface_color',
                'surface_tint',
                'text_color',
                'muted_text_color',
                'border_color',
                'ring_color',
                'button_radius',
                'card_radius',
                'focus_ring_color',
            ]);
        });
    }
};
