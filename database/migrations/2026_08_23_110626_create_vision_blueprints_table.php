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
        Schema::create('vision_blueprints', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('client_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->json('service_options')->nullable();
            $table->string('project_status')->default('Prospecting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vision_blueprints');
    }
};
