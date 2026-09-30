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
        Schema::create('security_threat_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('ip_address', 45)->index();
            $table->text('user_agent')->nullable();
            $table->string('endpoint')->index();
            $table->string('http_method', 10)->default('POST');
            $table->string('threat_type')->index(); // 'rce_attempt', 'command_injection', 'deserialization', 'rapid_probing', 'malicious_payload'
            $table->text('matched_pattern')->nullable();
            $table->longText('payload_sample')->nullable();
            $table->boolean('is_blocked')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_threat_logs');
    }
};
