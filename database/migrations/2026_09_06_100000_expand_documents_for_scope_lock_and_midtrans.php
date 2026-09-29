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
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'scope_locked')) {
                $table->boolean('scope_locked')->default(false);
            }
            if (!Schema::hasColumn('documents', 'contract_amount')) {
                $table->decimal('contract_amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('documents', 'dp_amount')) {
                $table->decimal('dp_amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('documents', 'midtrans_order_id')) {
                $table->string('midtrans_order_id')->nullable();
            }
            if (!Schema::hasColumn('documents', 'midtrans_payment_url')) {
                $table->text('midtrans_payment_url')->nullable();
            }
            if (!Schema::hasColumn('documents', 'content_clauses')) {
                $table->json('content_clauses')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $columns = [
                'scope_locked',
                'contract_amount',
                'dp_amount',
                'midtrans_order_id',
                'midtrans_payment_url',
                'content_clauses',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('documents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
