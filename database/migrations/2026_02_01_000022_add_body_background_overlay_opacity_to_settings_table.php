<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('body_background_overlay_opacity', 3, 2)->nullable()->default(0.90);
        });

        DB::table('settings')
            ->whereNull('body_background_overlay_opacity')
            ->update(['body_background_overlay_opacity' => 0.90]);
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('body_background_overlay_opacity');
        });
    }
};
