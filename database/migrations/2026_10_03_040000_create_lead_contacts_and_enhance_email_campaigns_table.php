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
        // 1. Lead Contacts Table (Strict ULID & PostgreSQL / MySQL agnostic)
        if (!Schema::hasTable('lead_contacts')) {
            Schema::create('lead_contacts', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('name');
                $table->string('email')->index();
                $table->string('company_name')->nullable()->index();
                $table->string('job_title')->nullable();
                $table->string('phone')->nullable();
                $table->string('status')->default('lead'); // lead, prospect, client, partner, archived
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        // 2. Enhance Email Campaigns with custom sender, reply-to, and direct recipient fields
        if (Schema::hasTable('email_campaigns')) {
            Schema::table('email_campaigns', function (Blueprint $table) {
                if (!Schema::hasColumn('email_campaigns', 'sender_name')) {
                    $table->string('sender_name')->nullable()->default('Yoseph Iriandi - Neriah Pro');
                }
                if (!Schema::hasColumn('email_campaigns', 'sender_email')) {
                    $table->string('sender_email')->nullable()->default('yoseph@neriahpro.com');
                }
                if (!Schema::hasColumn('email_campaigns', 'reply_to_email')) {
                    $table->string('reply_to_email')->nullable()->default('yoseph.iriandi.tambunan@gmail.com');
                }
                if (!Schema::hasColumn('email_campaigns', 'reply_to_name')) {
                    $table->string('reply_to_name')->nullable()->default('Yoseph Iriandi');
                }
                if (!Schema::hasColumn('email_campaigns', 'custom_recipient_email')) {
                    $table->string('custom_recipient_email')->nullable();
                }
                if (!Schema::hasColumn('email_campaigns', 'custom_recipient_name')) {
                    $table->string('custom_recipient_name')->nullable();
                }
                if (!Schema::hasColumn('email_campaigns', 'custom_company_name')) {
                    $table->string('custom_company_name')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('email_campaigns')) {
            Schema::table('email_campaigns', function (Blueprint $table) {
                $table->dropColumn([
                    'sender_name',
                    'sender_email',
                    'reply_to_email',
                    'reply_to_name',
                    'custom_recipient_email',
                    'custom_recipient_name',
                    'custom_company_name',
                ]);
            });
        }

        Schema::dropIfExists('lead_contacts');
    }
};
