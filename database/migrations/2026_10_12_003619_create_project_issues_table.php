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
        Schema::create('project_issues', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('vision_blueprint_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('feature_id')->nullable()->index(); // To link issue to a specific feature in PRD
            $table->json('title'); // Tier 1 Multi-lang JSON
            $table->json('description'); // Tier 1 Multi-lang JSON
            $table->string('status')->default('open')->index();
            $table->string('priority')->default('medium');
            $table->string('type')->default('bug')->index();
            $table->boolean('is_approved_for_uat')->default(false);
            $table->json('resolution_notes')->nullable(); // Tier 1 Multi-lang JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_issues');
    }
};
