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
        Schema::create('legal_policies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('type')->unique(); // e.g. 'terms_and_conditions', 'refund_policy'
            $table->json('title'); // multi-language
            $table->json('content'); // multi-language
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_policies');
    }
};
