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
        if (!Schema::hasTable('domain_hosting_assets')) {
            Schema::create('domain_hosting_assets', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('vision_blueprint_id')->nullable()->constrained('vision_blueprints')->nullOnDelete();
                $table->string('asset_type')->default('domain'); // domain, hosting, vps, ssl, email, bundle
                $table->string('name');
                $table->string('domain_name')->nullable()->index();
                $table->string('provider'); // Niagahoster, DomaiNesia, Cloudflare, Hetzner, etc.
                $table->string('server_ip')->nullable();
                $table->string('panel_url')->nullable();
                $table->date('purchase_date');
                $table->date('expires_at')->index();
                $table->string('billing_cycle')->default('yearly'); // monthly, yearly, 2_years, 3_years
                $table->decimal('cost_price', 15, 2)->default(0.00);
                $table->decimal('client_price', 15, 2)->nullable();
                $table->string('currency', 10)->default('IDR');
                $table->boolean('auto_renew')->default(false);
                $table->string('status')->default('active')->index(); // active, expiring_soon, expired, cancelled
                $table->integer('reminder_days_before')->default(30);
                $table->timestamp('last_reminder_sent_at')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_hosting_assets');
    }
};
