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
        Schema::create('translation_strings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('group')->default('*')->index();
            $table->string('key')->index();
            $table->jsonb('text')->nullable(); // For multi-language Tier 1/Tier 2
            $table->timestamps();

            $table->unique(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_strings');
    }
};
