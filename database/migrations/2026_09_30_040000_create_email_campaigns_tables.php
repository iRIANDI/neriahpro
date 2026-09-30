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
        // 1. Email Campaigns Table (Strict ULID & PostgreSQL compatible)
        if (!Schema::hasTable('email_campaigns')) {
            Schema::create('email_campaigns', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title');
                $table->string('subject');
                $table->string('preview_text')->nullable();
                $table->string('target_audience')->default('all'); // all, onboarding_clients, blueprint_clients, cv_users, custom
                $table->text('content_html');
                $table->string('cta_label')->nullable()->default('Pelajari Selengkapnya');
                $table->string('cta_url')->nullable();
                $table->string('status')->default('draft'); // draft, scheduled, sending, sent, failed
                $table->integer('total_recipients')->default(0);
                $table->integer('sent_count')->default(0);
                $table->integer('failed_count')->default(0);
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
        }

        // 2. Email Campaign Dispatch Logs
        if (!Schema::hasTable('email_campaign_logs')) {
            Schema::create('email_campaign_logs', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('email_campaign_id')->constrained('email_campaigns')->cascadeOnDelete();
                $table->string('recipient_email');
                $table->string('recipient_name')->nullable();
                $table->string('status')->default('sent'); // sent, failed
                $table->text('error_message')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_campaign_logs');
        Schema::dropIfExists('email_campaigns');
    }
};
