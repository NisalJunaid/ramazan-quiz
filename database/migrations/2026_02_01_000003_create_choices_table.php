<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->text('choice_text');
            $table->boolean('is_correct');
            $table->integer('order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('choices');
    }
};
