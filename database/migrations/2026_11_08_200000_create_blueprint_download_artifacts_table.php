<?php

declare(strict_types=1);

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
        if (! Schema::hasTable('blueprint_download_artifacts')) {
            Schema::create('blueprint_download_artifacts', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->foreignUlid('vision_blueprint_id')->constrained('vision_blueprints')->cascadeOnDelete();
                $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('client_identifier', 64)->index();
                $table->string('artifact_type', 32)->index(); // 'pdf', 'markdown', 'scaffold_zip'
                $table->string('locale', 10)->default('id');
                $table->string('title', 190);
                $table->string('filename', 190);
                $table->string('file_path')->nullable();
                $table->string('disk', 32)->default('local');
                $table->unsignedBigInteger('file_size_bytes')->nullable();
                $table->string('mime_type', 100)->nullable();
                $table->string('status', 32)->default('queued')->index(); // 'queued', 'processing', 'completed', 'failed', 'expired'
                $table->unsignedTinyInteger('progress')->default(0);
                $table->text('error_message')->nullable();
                $table->string('download_token', 64)->unique();
                $table->unsignedInteger('download_count')->default(0);
                $table->timestamp('last_downloaded_at')->nullable();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['vision_blueprint_id', 'client_identifier', 'artifact_type'], 'bda_blueprint_client_type_idx');
                $table->index(['expires_at', 'status'], 'bda_expires_status_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blueprint_download_artifacts');
    }
};

