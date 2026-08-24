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
        Schema::create('products', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->json('name'); // multi-language
            $table->json('description')->nullable(); // multi-language
            $table->json('features')->nullable(); // multi-language
            $table->decimal('price_idr', 15, 2)->default(0);
            $table->decimal('price_usd', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
