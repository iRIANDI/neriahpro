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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone_country_code')) {
                $table->string('phone_country_code', 10)->default('+62')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('phone_country_code');
            }
            if (!Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'npwp')) {
                $table->string('npwp', 50)->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('users', 'billing_address')) {
                $table->text('billing_address')->nullable()->after('npwp');
            }
            if (!Schema::hasColumn('users', 'billing_city')) {
                $table->string('billing_city', 100)->nullable()->after('billing_address');
            }
            if (!Schema::hasColumn('users', 'billing_province')) {
                $table->string('billing_province', 100)->nullable()->after('billing_city');
            }
            if (!Schema::hasColumn('users', 'billing_postal_code')) {
                $table->string('billing_postal_code', 20)->nullable()->after('billing_province');
            }
            if (!Schema::hasColumn('users', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('billing_postal_code');
            }
            if (!Schema::hasColumn('users', 'profile_metadata')) {
                $table->json('profile_metadata')->nullable()->after('notification_preferences');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [
                'phone_country_code',
                'phone',
                'company_name',
                'npwp',
                'billing_address',
                'billing_city',
                'billing_province',
                'billing_postal_code',
                'notification_preferences',
                'profile_metadata',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
