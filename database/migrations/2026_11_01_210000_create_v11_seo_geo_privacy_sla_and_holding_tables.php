<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pos_sla_maintenance_tickets')) {
            Schema::create('pos_sla_maintenance_tickets', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->foreignUlid('pos_project_id')->nullable()->constrained('pos_projects')->nullOnDelete();
                $table->foreignUlid('vision_blueprint_id')->nullable()->constrained('vision_blueprints')->nullOnDelete();
                $table->foreignUlid('reporter_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('ticket_number', 40)->unique();
                $table->string('reporter_name', 160);
                $table->string('reporter_email', 190)->index();
                $table->string('reporter_phone_country_code', 10)->default('+62');
                $table->string('reporter_phone', 32)->nullable();
                $table->string('issue_title', 200);
                $table->text('issue_description');
                $table->string('classification', 40)->default('PENDING_TRIAGE')->index();
                $table->unsignedTinyInteger('confidence_score')->default(95);
                $table->string('locked_scope_hash_v1', 64)->nullable();
                $table->json('ai_triage_rationale')->nullable();
                $table->longText('developer_bugfix_prompt')->nullable();
                $table->unsignedInteger('estimated_delta_hours')->default(0);
                $table->unsignedBigInteger('estimated_delta_idr')->default(0);
                $table->foreignUlid('linked_change_request_id')->nullable()->constrained('pos_change_requests')->nullOnDelete();
                $table->string('status', 32)->default('OPEN')->index();
                $table->timestamp('warranty_expires_at')->nullable();
                $table->timestamps();

                $table->index(['pos_project_id', 'classification', 'id'], 'pos_sla_proj_class_idx');
            });
        }

        if (! Schema::hasTable('pos_privacy_erasure_requests')) {
            Schema::create('pos_privacy_erasure_requests', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('requester_email');
                $table->char('email_blind_index', 64)->index();
                $table->text('requester_phone_e164')->nullable();
                $table->char('phone_blind_index', 64)->nullable()->index();
                $table->string('request_type', 40)->default('DATA_PORTABILITY_EXPORT')->index();
                $table->string('status', 32)->default('COMPLETED')->index();
                $table->string('redaction_token', 64)->nullable()->index();
                $table->json('redacted_fields_summary_json')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('pos_organization_workspaces')) {
            Schema::table('pos_organization_workspaces', function (Blueprint $table): void {
                if (! Schema::hasColumn('pos_organization_workspaces', 'parent_organization_id')) {
                    $table->char('parent_organization_id', 26)->nullable()->after('owner_user_id')->index();
                }
                if (! Schema::hasColumn('pos_organization_workspaces', 'entity_level')) {
                    $table->string('entity_level', 32)->default('HOLDING_HQ')->after('plan_tier')->index();
                }
                if (! Schema::hasColumn('pos_organization_workspaces', 'elimination_account_code')) {
                    $table->string('elimination_account_code', 32)->nullable()->after('entity_level');
                }
            });

            try {
                Schema::table('pos_organization_workspaces', function (Blueprint $table): void {
                    $table->foreign('parent_organization_id', 'pos_org_ws_parent_ulid_fk')
                        ->references('id')
                        ->on('pos_organization_workspaces')
                        ->nullOnDelete();
                });
            } catch (Throwable) {
                // Safe no-op if FK already exists or SQLite driver alters column independently
            }
        }

        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table): void {
                if (! Schema::hasColumn('pos_projects', 'sla_warranty_days')) {
                    $table->unsignedSmallInteger('sla_warranty_days')->default(90)->after('architectural_freeze_score');
                }
                if (! Schema::hasColumn('pos_projects', 'sla_warranty_expires_at')) {
                    $table->timestamp('sla_warranty_expires_at')->nullable()->after('sla_warranty_days');
                }
                if (! Schema::hasColumn('pos_projects', 'sla_maintenance_active')) {
                    $table->boolean('sla_maintenance_active')->default(false)->after('sla_warranty_expires_at')->index();
                }
                if (! Schema::hasColumn('pos_projects', 'seo_geo_contract_json')) {
                    $table->json('seo_geo_contract_json')->nullable()->after('sla_maintenance_active');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_privacy_erasure_requests');
        Schema::dropIfExists('pos_sla_maintenance_tickets');
    }
};
