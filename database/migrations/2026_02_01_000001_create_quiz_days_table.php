<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_days', function (Blueprint $table) {
            $table->id();
            $table->date('quiz_date')->unique();
            $table->string('title');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->integer('duration_seconds');
            $table->boolean('is_published');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_days');
    }
};
