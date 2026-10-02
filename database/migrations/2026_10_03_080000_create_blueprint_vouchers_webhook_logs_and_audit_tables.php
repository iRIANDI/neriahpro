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
        // 1. Blueprint Vouchers & Promo Codes (PostgreSQL Strict ULID)
        Schema::create('blueprint_vouchers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->string('discount_type')->default('free'); // free (100% bypass Rp 0), percentage, fixed
            $table->decimal('discount_value', 15, 2)->default(0.00);
            $table->integer('max_uses')->nullable(); // null = unlimited
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->string('created_by')->nullable()->default('yoseph.iriandi.tambunan@gmail.com');
            $table->timestamps();
        });

        // 2. Automated Webhook Dead-Letter Queue (DLQ) & Raw Payload Logs (PostgreSQL Strict ULID)
        Schema::create('payment_webhook_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('gateway')->default('midtrans');
            $table->string('event_type')->nullable(); // settlement, capture, pending, expire, cancel
            $table->string('order_id')->nullable()->index();
            $table->string('status')->default('received'); // received, processed, failed, ignored
            $table->json('raw_payload');
            $table->json('raw_headers')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // 3. Enhance vision_blueprints with Digital Sign-off Lock, Staging URL & Free Voucher Grant
        Schema::table('vision_blueprints', function (Blueprint $table) {
            $table->string('voucher_code')->nullable()->after('project_status');
            $table->boolean('is_free_grant')->default(false)->after('voucher_code');
            $table->boolean('signed_agreement')->default(false)->after('is_free_grant');
            $table->string('signer_ip')->nullable()->after('signed_agreement');
            $table->text('signer_user_agent')->nullable()->after('signer_ip');
            $table->string('document_sha256', 64)->nullable()->after('signer_user_agent');
            $table->timestamp('signed_at')->nullable()->after('document_sha256');
            $table->string('staging_url')->nullable()->after('signed_at');
            $table->timestamp('staging_provisioned_at')->nullable()->after('staging_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vision_blueprints', function (Blueprint $table) {
            $table->dropColumn([
                'voucher_code',
                'is_free_grant',
                'signed_agreement',
                'signer_ip',
                'signer_user_agent',
                'document_sha256',
                'signed_at',
                'staging_url',
                'staging_provisioned_at',
            ]);
        });

        Schema::dropIfExists('payment_webhook_logs');
        Schema::dropIfExists('blueprint_vouchers');
    }
};
