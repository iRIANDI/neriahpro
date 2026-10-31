<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table): void {
                if (! Schema::hasColumn('pos_projects', 'engagement_mode')) {
                    $table->string('engagement_mode', 32)->default('RETAIL_SELF_DEV')->index();
                }
                if (! Schema::hasColumn('pos_projects', 'scope_hash_sha256')) {
                    $table->string('scope_hash_sha256', 64)->nullable()->index();
                }
                if (! Schema::hasColumn('pos_projects', 'scope_locked_at')) {
                    $table->timestamp('scope_locked_at')->nullable();
                }
                if (! Schema::hasColumn('pos_projects', 'commercial_quotation_json')) {
                    $table->json('commercial_quotation_json')->nullable();
                }
                if (! Schema::hasColumn('pos_projects', 'uat_checklist_json')) {
                    $table->json('uat_checklist_json')->nullable();
                }
                if (! Schema::hasColumn('pos_projects', 'bast_signoff_json')) {
                    $table->json('bast_signoff_json')->nullable();
                }
            });
        }

        if (Schema::hasTable('pos_change_requests')) {
            Schema::table('pos_change_requests', function (Blueprint $table): void {
                if (! Schema::hasColumn('pos_change_requests', 'delta_man_hours')) {
                    $table->unsignedInteger('delta_man_hours')->default(0);
                }
                if (! Schema::hasColumn('pos_change_requests', 'delta_working_days')) {
                    $table->unsignedInteger('delta_working_days')->default(0);
                }
                if (! Schema::hasColumn('pos_change_requests', 'delta_quotation_idr')) {
                    $table->decimal('delta_quotation_idr', 19, 2)->default(0);
                }
                if (! Schema::hasColumn('pos_change_requests', 'addendum_status')) {
                    $table->string('addendum_status', 40)->default('DRAFT_ADDENDUM')->index();
                }
                if (! Schema::hasColumn('pos_change_requests', 'midtrans_addendum_order_id')) {
                    $table->string('midtrans_addendum_order_id', 80)->nullable()->index();
                }
                if (! Schema::hasColumn('pos_change_requests', 'approved_by_client_at')) {
                    $table->timestamp('approved_by_client_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pos_change_requests')) {
            Schema::table('pos_change_requests', function (Blueprint $table): void {
                $cols = array_filter([
                    'delta_man_hours',
                    'delta_working_days',
                    'delta_quotation_idr',
                    'addendum_status',
                    'midtrans_addendum_order_id',
                    'approved_by_client_at',
                ], fn (string $c) => Schema::hasColumn('pos_change_requests', $c));

                if (! empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table): void {
                $cols = array_filter([
                    'engagement_mode',
                    'scope_hash_sha256',
                    'scope_locked_at',
                    'commercial_quotation_json',
                    'uat_checklist_json',
                    'bast_signoff_json',
                ], fn (string $c) => Schema::hasColumn('pos_projects', $c));

                if (! empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};

