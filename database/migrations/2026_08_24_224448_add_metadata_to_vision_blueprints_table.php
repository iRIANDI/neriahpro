<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vision_blueprints', function (Blueprint $table) {
            $table->string('ip_address')->nullable();
            $table->json('user_metadata')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vision_blueprints', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'user_metadata']);
        });
    }
};
