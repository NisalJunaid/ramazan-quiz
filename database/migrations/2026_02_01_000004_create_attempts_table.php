<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_day_id')->constrained('quiz_days');
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('started_at');
            $table->dateTime('expires_at');
            $table->dateTime('submitted_at')->nullable();
            $table->integer('score')->default(0);
            $table->enum('status', ['in_progress', 'submitted', 'expired'])->default('in_progress');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['quiz_day_id', 'user_id']);
            $table->index(['quiz_day_id', 'score', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};
