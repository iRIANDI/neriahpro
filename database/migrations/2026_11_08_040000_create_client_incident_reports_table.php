<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('client_incident_reports')) {
            Schema::create('client_incident_reports', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->string('incident_code', 40)->unique();
                $table->foreignUlid('vision_blueprint_id')->nullable()->constrained('vision_blueprints')->nullOnDelete();
                $table->string('client_email', 190)->index();
                $table->string('client_name', 160)->nullable();
                $table->string('client_phone', 32)->nullable();
                $table->string('error_type', 50)->default('AI_SYNTHESIS_TIMEOUT')->index();
                $table->text('error_message');
                $table->json('technical_diagnostics')->nullable();
                $table->text('client_notes')->nullable();
                $table->string('status', 32)->default('COMPENSATED')->index();
                $table->string('compensation_type', 64)->default('SERVICE_CREDIT_AND_SLA_WARRANTY');
                $table->string('compensation_voucher_code', 50)->nullable()->index();
                $table->unsignedBigInteger('compensation_amount_idr')->default(150000);
                $table->unsignedInteger('extra_sla_warranty_days')->default(30);
                $table->boolean('is_compensated')->default(true);
                $table->string('ip_address', 45)->index();
                $table->char('idempotency_hash', 64)->index();
                $table->timestamps();

                $table->index(['client_email', 'status', 'id'], 'client_inc_email_status_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('client_incident_reports');
    }
};

