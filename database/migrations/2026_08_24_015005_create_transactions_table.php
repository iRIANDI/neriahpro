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
        Schema::create('transactions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('midtrans_transaction_id')->nullable()->unique();
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->enum('status', ['pending', 'settlement', 'expire', 'cancel', 'deny', 'refund'])->default('pending');
            $table->decimal('total_idr', 15, 2);
            $table->string('original_currency')->default('IDR');
            $table->decimal('original_amount', 15, 2)->nullable();
            $table->decimal('exchange_rate', 15, 4)->nullable(); // kurs tukar
            $table->json('customer_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
