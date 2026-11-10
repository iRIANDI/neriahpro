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
        if (! Schema::hasTable('cv_finance_records')) {
            Schema::create('cv_finance_records', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('trx_code', 32)->unique();
                $table->date('transaction_date');
                $table->string('client_name');
                $table->string('client_email')->nullable();
                $table->string('package_name');
                $table->decimal('amount_idr', 12, 2)->default(0);
                $table->string('payment_method')->default('Midtrans QRIS');
                $table->string('status')->default('paid'); // 'paid', 'pending', 'cancelled'
                $table->text('notes')->nullable();
                $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['status', 'transaction_date']);
                $table->index('client_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_finance_records');
    }
};

