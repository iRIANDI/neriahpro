<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Sektor Bisnis (12 Sektor Fungsional Domain-Driven)
        Schema::create('pos_sectors', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('code', 64)->unique(); // e.g., SECTOR_02_HOSPITALITY_SPACE
            $table->unsignedTinyInteger('sector_number')->default(1)->index();
            $table->jsonb('name'); // {"id": "Perhotelan, Properti & Reservasi Aset", "en": "Hospitality, Travel & Space"}
            $table->jsonb('description')->nullable();
            $table->jsonb('domain_terminology'); // {"customer": "Guest", "item": "Room/Court"}
            $table->jsonb('blueprint_invariants');
            $table->jsonb('default_compliance_standards'); // ["PSAK 109", "ISAK 35", "UU PDP"]
            $table->jsonb('detection_keywords')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 2. Tabel Modul Primitif Lintas Sektor (Core Primitives)
        Schema::create('pos_primitives', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('code', 64)->unique(); // e.g., PRIMITIVE_TIME_SLOT_ENGINE
            $table->jsonb('name');
            $table->string('category', 32)->index(); // COMMERCE, LEDGER, SCHEDULING, WORKFLOW, CLINICAL, LOGISTICS, MEDIA, HR, GOVTECH
            $table->jsonb('description')->nullable();
            $table->jsonb('schema_blueprints'); // Array definisi tabel, kolom, tipe data eksak, & index
            $table->jsonb('state_machines'); // Array definisi Enum, transisi From->To, Guards, Side-Effects
            $table->jsonb('concurrency_contracts'); // Aturan lockForUpdate(), TTL, & Idempotency
            $table->jsonb('rbac_permissions')->nullable(); // Matriks Policy per Action
            $table->jsonb('edge_case_playbooks'); // Skenario konflik & resolusi deterministik
            $table->jsonb('gemini_tool_declarations'); // Definisi Native Function Calling untuk modul ini
            $table->jsonb('detection_keywords')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 3. Pivot Sektor <-> Primitif (Rekomendasi Bawaan per Sektor)
        Schema::create('pos_sector_primitive', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('sector_id')->constrained('pos_sectors')->cascadeOnDelete();
            $table->foreignUlid('primitive_id')->constrained('pos_primitives')->cascadeOnDelete();
            $table->boolean('is_mandatory')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['sector_id', 'primitive_id'], 'pos_sector_primitive_unique');
        });

        // 4. Tabel Master Aturan Anti-Slop (UI/UX, Database, Backend, Infrastructure, Agent Harness)
        Schema::create('pos_rule_packs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('slug', 64)->unique(); // e.g., anti-slop-ui-linear-style, strict-pgsql-ulid
            $table->jsonb('name')->nullable();
            $table->string('layer', 32)->index(); // UI_UX, DATABASE, BACKEND, INFRASTRUCTURE, AGENT_HARNESS
            $table->string('enforcement_level', 16)->default('HARAM'); // HARAM (Forbidden), WAJIB (Mandatory)
            $table->text('bad_pattern_example');
            $table->text('good_pattern_example');
            $table->longText('markdown_directive');
            $table->boolean('is_global_default')->default(true)->index();
            $table->timestamps();
        });

        // 5. Tabel Proyek Pengguna (Tenant / User Blueprints yang sedang dirakit)
        Schema::create('pos_projects', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('vision_blueprint_id')->nullable()->constrained('vision_blueprints')->cascadeOnDelete();
            $table->foreignUlid('primary_sector_id')->constrained('pos_sectors');
            $table->string('app_name');
            $table->string('architecture_archetype', 32)->default('COMPOSITE');
            $table->string('tenancy_model', 64)->default('MULTI_TENANT_SHARED_DB_COLUMN_SCOPED');
            $table->string('tenant_identifier', 64)->default('organization_id');
            $table->string('stack_preset', 96)->default('LARAVEL13_FILAMENT5_LIVEWIRE4_FLUX_PGSQL16_RAILPACK');
            $table->jsonb('selected_primitive_ids');
            $table->jsonb('selected_rule_pack_ids');
            $table->jsonb('custom_entities')->nullable();
            $table->longText('compiled_schema_json')->nullable();
            $table->longText('compiled_execution_md')->nullable();
            $table->longText('compiled_agents_md')->nullable();
            $table->longText('compiled_graph_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_projects');
        Schema::dropIfExists('pos_rule_packs');
        Schema::dropIfExists('pos_sector_primitive');
        Schema::dropIfExists('pos_primitives');
        Schema::dropIfExists('pos_sectors');
    }
};

