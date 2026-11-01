<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pos_projects')) {
            return;
        }

        Schema::table('pos_projects', function (Blueprint $table) {
            if (! Schema::hasColumn('pos_projects', 'persona_mode')) {
                $table->string('persona_mode', 32)->default('ARCHITECT_PRO')->after('engagement_mode');
            }
            if (! Schema::hasColumn('pos_projects', 'selected_ui_variant')) {
                $table->string('selected_ui_variant', 80)->nullable()->after('persona_mode');
            }
            if (! Schema::hasColumn('pos_projects', 'navigation_shell')) {
                $table->string('navigation_shell', 48)->nullable()->after('selected_ui_variant');
            }
            if (! Schema::hasColumn('pos_projects', 'output_locale')) {
                $table->string('output_locale', 16)->default('id')->after('navigation_shell');
            }
            if (! Schema::hasColumn('pos_projects', 'jurisdiction_profile')) {
                $table->string('jurisdiction_profile', 40)->nullable()->after('output_locale');
            }
            if (! Schema::hasColumn('pos_projects', 'white_label_branding_json')) {
                $table->json('white_label_branding_json')->nullable()->after('client_sop_onboarding_json');
            }
            if (! Schema::hasColumn('pos_projects', 'encrypted_custom_vault')) {
                $table->text('encrypted_custom_vault')->nullable()->after('white_label_branding_json');
            }
            if (! Schema::hasColumn('pos_projects', 'canary_watermark_sig')) {
                $table->string('canary_watermark_sig', 32)->nullable()->after('encrypted_custom_vault');
            }
            if (! Schema::hasColumn('pos_projects', 'cve_alerts_ack_json')) {
                $table->json('cve_alerts_ack_json')->nullable()->after('canary_watermark_sig');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pos_projects')) {
            return;
        }

        $dropCols = array_values(array_filter([
            Schema::hasColumn('pos_projects', 'persona_mode') ? 'persona_mode' : null,
            Schema::hasColumn('pos_projects', 'selected_ui_variant') ? 'selected_ui_variant' : null,
            Schema::hasColumn('pos_projects', 'navigation_shell') ? 'navigation_shell' : null,
            Schema::hasColumn('pos_projects', 'output_locale') ? 'output_locale' : null,
            Schema::hasColumn('pos_projects', 'jurisdiction_profile') ? 'jurisdiction_profile' : null,
            Schema::hasColumn('pos_projects', 'white_label_branding_json') ? 'white_label_branding_json' : null,
            Schema::hasColumn('pos_projects', 'encrypted_custom_vault') ? 'encrypted_custom_vault' : null,
            Schema::hasColumn('pos_projects', 'canary_watermark_sig') ? 'canary_watermark_sig' : null,
            Schema::hasColumn('pos_projects', 'cve_alerts_ack_json') ? 'cve_alerts_ack_json' : null,
        ]));

        if (! empty($dropCols)) {
            Schema::table('pos_projects', function (Blueprint $table) use ($dropCols) {
                $table->dropColumn($dropCols);
            });
        }
    }
};

