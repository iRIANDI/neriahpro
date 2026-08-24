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
        Schema::create('client_onboardings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('email');
            $table->string('company_name')->nullable();
            $table->json('project_needs'); // For dynamic questionnaire answers
            $table->string('budget_range');
            $table->boolean('privacy_consent_agreed')->default(false);
            $table->string('status')->default('new_lead');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_onboardings');
    }
};
