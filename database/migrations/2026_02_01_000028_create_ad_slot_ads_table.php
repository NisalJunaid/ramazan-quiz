<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_slot_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_slot_id')->constrained('ad_slots')->cascadeOnDelete();
            $table->foreignId('ad_id')->constrained('ads')->cascadeOnDelete();
            $table->unsignedInteger('weight')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['ad_slot_id', 'ad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_slot_ads');
    }
};
