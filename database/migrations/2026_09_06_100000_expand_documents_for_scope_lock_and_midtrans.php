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
            $table->boolean('scope_locked')->default(false)->after('status');
            $table->decimal('contract_amount', 15, 2)->nullable()->after('scope_locked');
            $table->decimal('dp_amount', 15, 2)->nullable()->after('contract_amount');
            $table->string('midtrans_order_id')->nullable()->after('dp_amount');
            $table->text('midtrans_payment_url')->nullable()->after('midtrans_order_id');
            $table->json('content_clauses')->nullable()->after('midtrans_payment_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'scope_locked',
                'contract_amount',
                'dp_amount',
                'midtrans_order_id',
                'midtrans_payment_url',
                'content_clauses',
            ]);
        });
    }
};
