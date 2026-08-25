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
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->json('plugins')->nullable()->after('is_published');
        });

        Schema::dropIfExists('cms_plugins');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropColumn('plugins');
        });

        Schema::create('cms_plugins', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('cms_page_id')->constrained()->cascadeOnDelete();
            $table->string('plugin_type');
            $table->json('content_data')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
