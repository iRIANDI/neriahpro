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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('code')->unique(); // e.g. customer_otp, project_blueprint, invoice_receipt
            $table->string('name'); // e.g. Template OTP Autentikasi Klien
            $table->json('subject'); // e.g. {"id": "...", "en": "..."}
            $table->string('sender_name')->nullable()->default('Neriah Pro Support');
            $table->string('sender_email')->nullable()->default('support@neriahpro.com');
            $table->string('reply_to_email')->nullable()->default('support@neriahpro.com');
            $table->json('body_html'); // e.g. {"id": "...", "en": "..."}
            $table->json('available_placeholders')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
