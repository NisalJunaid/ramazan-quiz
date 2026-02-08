<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_slots', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->enum('mode', ['fixed', 'rotating']);
            $table->foreignId('fixed_ad_id')->nullable()->constrained('ads')->nullOnDelete();
            $table->enum('rotation_strategy', ['random', 'sequential'])->default('random');
            $table->unsignedInteger('rotation_seconds')->nullable();
            $table->timestamps();
        });

        DB::table('ad_slots')->insert([
            'key' => 'home_top',
            'mode' => 'fixed',
            'fixed_ad_id' => null,
            'rotation_strategy' => 'random',
            'rotation_seconds' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_slots');
    }
};
