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
            $table->string('e_meterai_status')->default('none')->after('status');
            $table->string('e_meterai_sn')->nullable()->after('e_meterai_status');
            $table->timestamp('e_meterai_stamped_at')->nullable()->after('e_meterai_sn');
            $table->boolean('scope_locked')->default(false)->after('e_meterai_stamped_at');
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
                'e_meterai_status',
                'e_meterai_sn',
                'e_meterai_stamped_at',
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
