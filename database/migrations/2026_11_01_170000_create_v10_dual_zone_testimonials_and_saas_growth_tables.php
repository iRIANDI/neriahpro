<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Additive Dual-Zone UI & Organization Workspace columns on pos_projects
        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table): void {
                if (! Schema::hasColumn('pos_projects', 'public_ux_preset')) {
                    $table->string('public_ux_preset', 60)->nullable()->index();
                }
                if (! Schema::hasColumn('pos_projects', 'dual_zone_ui_contract_json')) {
                    $table->json('dual_zone_ui_contract_json')->nullable();
                }
                if (! Schema::hasColumn('pos_projects', 'organization_workspace_id')) {
                    $table->string('organization_workspace_id', 26)->nullable()->index();
                }
            });
        }

        // 2. Additive SaaS Billing Quota & Affiliate Referral columns on users
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                if (! Schema::hasColumn('users', 'referral_code')) {
                    $table->string('referral_code', 32)->nullable()->unique();
                }
                if (! Schema::hasColumn('users', 'referred_by_user_id')) {
                    $table->string('referred_by_user_id', 26)->nullable()->index();
                }
                if (! Schema::hasColumn('users', 'saas_subscription_tier')) {
                    $table->string('saas_subscription_tier', 40)->default('SPARK_FREE')->index();
                }
                if (! Schema::hasColumn('users', 'saas_subscription_expires_at')) {
                    $table->timestamp('saas_subscription_expires_at')->nullable();
                }
                if (! Schema::hasColumn('users', 'saas_project_quota')) {
                    $table->unsignedInteger('saas_project_quota')->default(1);
                }
            });
        }

        // 3. Cryptographically Verified Testimonial & Case-Study Engine (PRIMITIVE_VERIFIED_SOCIAL_PROOF)
        if (! Schema::hasTable('pos_verified_testimonials')) {
            Schema::create('pos_verified_testimonials', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->foreignUlid('pos_project_id')->nullable()->index();
                $table->foreignUlid('vision_blueprint_id')->nullable()->index();
                $table->foreignUlid('user_id')->nullable()->index();
                $table->string('sector_code', 60)->index();
                $table->string('stack_code', 60)->default('STACK_M1_LARAVEL_MONOLITH');
                $table->string('public_ux_preset', 60)->default('UX_F2_KINETIC_ANIWALL');
                $table->string('operational_ui_variant', 60)->nullable();
                $table->string('verification_trigger', 40)->default('MITRA_BAST_SIGNED')->index();
                $table->string('client_name', 120);
                $table->json('client_role')->nullable();
                $table->string('client_company', 140);
                $table->string('client_email', 160)->nullable();
                $table->string('client_phone_country_code', 10)->default('+62');
                $table->string('client_phone', 30)->nullable();
                $table->json('quote');
                $table->json('case_study_summary')->nullable();
                $table->json('architecture_receipt_json')->nullable();
                $table->json('anonymized_blueprint_json')->nullable();
                $table->unsignedTinyInteger('rating')->default(5);
                $table->boolean('is_featured')->default(true)->index();
                $table->boolean('is_published')->default(true)->index();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
            });
        }

        // 4. Affiliate, Referral & High-Ticket Mitra Finder's Fee Engine
        if (! Schema::hasTable('pos_affiliate_referrals')) {
            Schema::create('pos_affiliate_referrals', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->foreignUlid('referrer_user_id')->index();
                $table->foreignUlid('referred_user_id')->nullable()->index();
                $table->foreignUlid('vision_blueprint_id')->nullable()->index();
                $table->foreignUlid('transaction_id')->nullable()->index();
                $table->string('referral_code', 32)->index();
                $table->string('commission_type', 40)->default('RETAIL_RECURRING_20')->index();
                $table->bigInteger('gross_transaction_idr')->default(0);
                $table->decimal('commission_rate_percent', 5, 2)->default(20.00);
                $table->bigInteger('commission_amount_idr')->default(0);
                $table->string('status', 40)->default('REGISTERED')->index();
                $table->timestamp('settled_at')->nullable();
                $table->json('payout_metadata_json')->nullable();
                $table->timestamps();
            });
        }

        // 5. Multi-Seat Organization Workspace & Agency White-Label Vault
        if (! Schema::hasTable('pos_organization_workspaces')) {
            Schema::create('pos_organization_workspaces', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->foreignUlid('owner_user_id')->index();
                $table->string('name', 150);
                $table->string('slug', 160)->unique();
                $table->string('plan_tier', 40)->default('AGENCY_PARTNER')->index();
                $table->unsignedInteger('seat_limit')->default(5);
                $table->json('white_label_config_json')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pos_organization_members')) {
            Schema::create('pos_organization_members', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->foreignUlid('workspace_id')->index();
                $table->foreignUlid('user_id')->nullable()->index();
                $table->string('invited_email', 160)->index();
                $table->string('invited_phone_country_code', 10)->default('+62');
                $table->string('invited_phone', 30)->nullable();
                $table->string('workspace_role', 40)->default('JUNIOR_DEV')->index();
                $table->string('status', 30)->default('INVITED')->index();
                $table->string('invite_token_hash', 64)->nullable();
                $table->timestamps();
            });
        }

        // 6. Non-Profit, NGO & Academic 1-Click Impact Grant Verification Workflow
        if (! Schema::hasTable('pos_impact_grant_applications')) {
            Schema::create('pos_impact_grant_applications', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->foreignUlid('user_id')->nullable()->index();
                $table->string('applicant_name', 140);
                $table->string('organization_name', 160);
                $table->string('organization_type', 40)->default('NONPROFIT_YAYASAN')->index();
                $table->string('official_email', 160)->index();
                $table->string('phone_country_code', 10)->default('+62');
                $table->string('phone_number', 30);
                $table->string('legal_decree_number', 120)->nullable();
                $table->string('private_document_path', 255)->nullable();
                $table->bigInteger('requested_project_budget_idr')->default(0);
                $table->json('mission_statement')->nullable();
                $table->string('status', 40)->default('PENDING_REVIEW')->index();
                $table->foreignUlid('reviewed_by_user_id')->nullable()->index();
                $table->timestamp('reviewed_at')->nullable();
                $table->string('granted_voucher_code', 60)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_impact_grant_applications');
        Schema::dropIfExists('pos_organization_members');
        Schema::dropIfExists('pos_organization_workspaces');
        Schema::dropIfExists('pos_affiliate_referrals');
        Schema::dropIfExists('pos_verified_testimonials');

        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table): void {
                foreach (['public_ux_preset', 'dual_zone_ui_contract_json', 'organization_workspace_id'] as $col) {
                    if (Schema::hasColumn('pos_projects', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                foreach (['referral_code', 'referred_by_user_id', 'saas_subscription_tier', 'saas_subscription_expires_at', 'saas_project_quota'] as $col) {
                    if (Schema::hasColumn('users', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
