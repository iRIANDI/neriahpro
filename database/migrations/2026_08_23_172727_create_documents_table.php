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
        Schema::create('documents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('document_type')->default('contract');
            $table->nullableUlidMorphs('related'); // untuk polymorphic relation
            $table->string('file_path')->nullable();
            $table->enum('status', ['draft', 'pending_signature', 'signed'])->default('draft');
            
            // Signature Details
            $table->string('signer_name')->nullable();
            $table->string('signer_email')->nullable();
            $table->string('signer_ip_address')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->longText('digital_signature_image')->nullable(); // Base64
            $table->string('document_hash')->nullable(); // SHA-256

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
