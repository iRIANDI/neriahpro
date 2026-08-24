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
        Schema::create('cms_plugins', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('cms_page_id')->constrained()->cascadeOnDelete();
            $table->string('plugin_type'); // e.g. 'hero_section', 'feature_grid'
            $table->json('content_data'); // multi-language json
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_plugins');
    }
};
