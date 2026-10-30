<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Sessions for AI Chat / Copilot
        if (!Schema::hasTable('ai_chat_sessions')) {
            Schema::create('ai_chat_sessions', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('title', 255)->default('Percakapan AI Baru');
                $table->string('division', 100)->nullable();
                $table->string('provider', 50)->default('gemini');
                $table->string('status', 30)->default('active');
                $table->integer('message_count')->default(0);
                $table->integer('max_messages')->default(50);
                $table->boolean('is_pinned')->default(false);
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
            });
        }

        // 2. Chat Messages
        if (!Schema::hasTable('ai_chat_messages')) {
            Schema::create('ai_chat_messages', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('session_id')->constrained('ai_chat_sessions')->cascadeOnDelete();
                $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('role', 20)->default('user'); // user | assistant | system | tool
                $table->longText('content');
                $table->json('attachments')->nullable(); // Array of [{name, path, url, type, size, mime}]
                $table->json('tool_calls')->nullable(); // Function calls
                $table->json('tool_results')->nullable(); // Function call results
                $table->integer('tokens_used')->default(0);
                $table->string('provider', 50)->nullable();
                $table->timestamps();

                $table->index(['session_id', 'created_at']);
            });
        }

        // 3. Knowledge Vectors (RAG / CAG / KAG)
        if (!Schema::hasTable('ai_knowledge_vectors')) {
            if (DB::getDriverName() === 'pgsql') {
                try {
                    DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');
                } catch (\Throwable) {}
            }

            Schema::create('ai_knowledge_vectors', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('title')->nullable();
                $table->string('source_type')->default('document'); // blueprint, prd, policy, architecture, doc
                $table->string('source_id')->nullable();
                $table->text('content'); // Chunked text content
                $table->json('metadata')->nullable(); // author, chunk_index, total_chunks, tokens
                $table->longText('embedding_json')->nullable(); // Universal float array
                $table->timestamps();

                $table->index('source_type');
                $table->index('source_id');
                $table->index('created_at');
            });

            if (DB::getDriverName() === 'pgsql') {
                try {
                    DB::statement('ALTER TABLE ai_knowledge_vectors ADD COLUMN IF NOT EXISTS embedding vector(768);');
                    DB::statement('CREATE INDEX IF NOT EXISTS ai_knowledge_vectors_embedding_hnsw_idx ON ai_knowledge_vectors USING hnsw (embedding vector_cosine_ops);');
                } catch (\Throwable) {}
            }
        }

        // 4. AI Workflow Traces (CCTV / Decision Audit Trail)
        if (!Schema::hasTable('ai_workflow_traces')) {
            Schema::create('ai_workflow_traces', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('session_id', 50)->nullable()->index();
                $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('stage', 50)->default('analysis'); // ingestion, analysis, tool_invocation, db_execution, completed, failed
                $table->string('title', 255);
                $table->string('action_type', 100)->nullable()->index();
                $table->string('status', 30)->default('running'); // running, success, failed
                $table->json('input_payload')->nullable();
                $table->json('output_payload')->nullable();
                $table->string('affected_model', 150)->nullable();
                $table->string('affected_id', 50)->nullable();
                $table->string('url', 500)->nullable();
                $table->integer('execution_time_ms')->nullable();
                $table->text('summary')->nullable();
                $table->timestamps();

                $table->index(['session_id', 'created_at']);
                $table->index(['user_id', 'created_at']);
                $table->index(['stage', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_workflow_traces');
        Schema::dropIfExists('ai_knowledge_vectors');
        Schema::dropIfExists('ai_chat_messages');
        Schema::dropIfExists('ai_chat_sessions');
    }
};

