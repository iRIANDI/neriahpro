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
        // 1. Dynamic Plans & Top-Up Packages
        if (!Schema::hasTable('cv_pro_plans')) {
            Schema::create('cv_pro_plans', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('code')->unique(); // starter_free, pro_career, ultimate_exec, topup_tailor_5, etc.
                $table->string('type')->default('subscription'); // 'subscription' or 'topup'
                $table->json('name'); // {"id": "Pro Career", "en": "Pro Career"}
                $table->string('badge')->nullable(); // 'POPULER', 'BEST VALUE', 'HEMAT 40%'
                $table->json('description')->nullable();
                $table->decimal('price_idr', 12, 2)->default(0);
                $table->decimal('price_usd', 8, 2)->default(0);
                $table->string('billing_cycle')->default('monthly'); // 'monthly', 'yearly', 'one_time'
                $table->json('quotas'); // limits: tailor_cv, mock_interviews, ats_audits, linkedin_packs, outreach_letters, ai_credits
                $table->json('features')->nullable(); // bullet highlights
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2. User Quota & Entitlements
        if (!Schema::hasTable('user_cv_quotas')) {
            Schema::create('user_cv_quotas', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('user_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->foreignUlid('plan_id')->nullable()->constrained('cv_pro_plans')->nullOnDelete();
                $table->string('tier_code')->default('free');
                
                // Feature Quotas
                $table->integer('tailor_cv_quota')->default(1);
                $table->integer('tailor_cv_used')->default(0);
                
                $table->integer('mock_interviews_quota')->default(1);
                $table->integer('mock_interviews_used')->default(0);
                
                $table->integer('ats_audits_quota')->default(3);
                $table->integer('ats_audits_used')->default(0);
                
                $table->integer('linkedin_packs_quota')->default(0);
                $table->integer('linkedin_packs_used')->default(0);
                
                $table->integer('outreach_letters_quota')->default(1);
                $table->integer('outreach_letters_used')->default(0);
                
                $table->integer('ai_credits_balance')->default(10);
                
                $table->timestamp('plan_expires_at')->nullable();
                $table->timestamps();
            });
        }

        // 3. Quota Audit & Transactions Log (Top-up & Usage History)
        if (!Schema::hasTable('cv_quota_transactions')) {
            Schema::create('cv_quota_transactions', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignUlid('plan_id')->nullable()->constrained('cv_pro_plans')->nullOnDelete();
                $table->string('type'); // 'plan_grant', 'topup', 'usage_deduction', 'admin_adjustment'
                $table->string('feature'); // 'tailor_cv', 'mock_interview', 'ats_audit', 'linkedin_pack', 'outreach_letter', 'ai_credits'
                $table->integer('amount'); // +5, -1, etc.
                $table->integer('balance_after')->default(0);
                $table->string('description')->nullable();
                $table->foreignUlid('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_quota_transactions');
        Schema::dropIfExists('user_cv_quotas');
        Schema::dropIfExists('cv_pro_plans');
    }
};
