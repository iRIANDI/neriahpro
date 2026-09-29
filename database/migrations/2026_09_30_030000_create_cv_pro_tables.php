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
        // 1. Resumes table (Strict ULID & PostgreSQL compatible)
        if (!Schema::hasTable('resumes')) {
            Schema::create('resumes', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title')->default('Resume Saya');
                $table->string('slug')->unique();
                $table->string('target_role')->nullable();
                $table->string('template')->default('modern_minimalist'); // modern_minimalist, executive_clean, creative_ats, tech_dark
                $table->string('font_family')->default('Inter');
                $table->string('primary_color')->default('#4f46e5');
                $table->string('photo_url')->nullable();
                $table->json('content')->nullable(); // personal_info, experiences, education, skills, projects, certifications
                $table->json('section_order')->nullable();
                $table->boolean('is_public')->default(true);
                $table->integer('ats_score')->default(75);
                $table->json('ats_feedback')->nullable();
                $table->timestamps();
            });
        }

        // 2. Virtual Mock Interview Sessions
        if (!Schema::hasTable('interview_sessions')) {
            Schema::create('interview_sessions', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('resume_id')->nullable()->constrained('resumes')->cascadeOnDelete();
                $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('target_company');
                $table->string('job_title');
                $table->text('job_description')->nullable();
                $table->json('questions')->nullable();
                $table->json('answers')->nullable();
                $table->json('evaluation')->nullable();
                $table->integer('overall_score')->default(0);
                $table->timestamps();
            });
        }

        // 3. Post-Interview & Outreach Letters
        if (!Schema::hasTable('outreach_letters')) {
            Schema::create('outreach_letters', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('resume_id')->nullable()->constrained('resumes')->cascadeOnDelete();
                $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('letter_type')->default('thank_you'); // thank_you, follow_up, letter_of_interest
                $table->string('company_name');
                $table->string('recipient_name')->nullable();
                $table->text('generated_content');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outreach_letters');
        Schema::dropIfExists('interview_sessions');
        Schema::dropIfExists('resumes');
    }
};
