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
        Schema::table('vision_blueprints', function (Blueprint $table) {
            if (!Schema::hasColumn('vision_blueprints', 'nama_bisnis')) {
                $table->string('nama_bisnis')->nullable();
            }
            if (!Schema::hasColumn('vision_blueprints', 'masalah_utama')) {
                $table->text('masalah_utama')->nullable();
            }
            if (!Schema::hasColumn('vision_blueprints', 'tujuan_utama')) {
                $table->text('tujuan_utama')->nullable();
            }
            if (!Schema::hasColumn('vision_blueprints', 'target_audiens')) {
                $table->text('target_audiens')->nullable();
            }
            if (!Schema::hasColumn('vision_blueprints', 'aktor_sistem')) {
                $table->text('aktor_sistem')->nullable();
            }
            if (!Schema::hasColumn('vision_blueprints', 'fitur_wajib')) {
                $table->text('fitur_wajib')->nullable();
            }
            if (!Schema::hasColumn('vision_blueprints', 'fitur_tambahan')) {
                $table->text('fitur_tambahan')->nullable();
            }
            if (!Schema::hasColumn('vision_blueprints', 'alur_kerja')) {
                $table->text('alur_kerja')->nullable();
            }
            if (!Schema::hasColumn('vision_blueprints', 'kebutuhan_integrasi')) {
                $table->text('kebutuhan_integrasi')->nullable();
            }
            if (!Schema::hasColumn('vision_blueprints', 'referensi_desain')) {
                $table->text('referensi_desain')->nullable();
            }
            if (!Schema::hasColumn('vision_blueprints', 'kesiapan_aset')) {
                $table->string('kesiapan_aset')->nullable()->default('Belum Siap Sama Sekali');
            }
            if (!Schema::hasColumn('vision_blueprints', 'target_waktu')) {
                $table->string('target_waktu')->nullable();
            }
            if (!Schema::hasColumn('vision_blueprints', 'is_published')) {
                $table->boolean('is_published')->default(false);
            }
            if (!Schema::hasColumn('vision_blueprints', 'prd_content')) {
                $table->json('prd_content')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vision_blueprints', function (Blueprint $table) {
            $table->dropColumn([
                'nama_bisnis',
                'masalah_utama',
                'tujuan_utama',
                'target_audiens',
                'aktor_sistem',
                'fitur_wajib',
                'fitur_tambahan',
                'alur_kerja',
                'kebutuhan_integrasi',
                'referensi_desain',
                'kesiapan_aset',
                'target_waktu',
                'is_published',
                'prd_content',
            ]);
        });
    }
};
