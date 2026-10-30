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
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('actor_name', 255)->nullable();
                $table->string('actor_type', 50)->default('admin'); // admin, client, system, ai_agent
                $table->string('action', 100); // created, updated, deleted, login, 2fa_enabled, etc.
                $table->text('description')->nullable();
                $table->string('model_type', 150)->nullable();
                $table->string('model_id', 36)->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('context_url', 500)->nullable();
                $table->timestamps();

                $table->index(['model_type', 'model_id']);
                $table->index('action');
                $table->index('actor_type');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

