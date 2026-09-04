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
            $table->string('nama_bisnis')->nullable()->after('client_name');
            $table->text('masalah_utama')->nullable()->after('phone');
            $table->text('tujuan_utama')->nullable()->after('masalah_utama');
            $table->text('target_audiens')->nullable()->after('tujuan_utama');
            $table->text('aktor_sistem')->nullable()->after('target_audiens');
            $table->text('fitur_wajib')->nullable()->after('aktor_sistem');
            $table->text('fitur_tambahan')->nullable()->after('fitur_wajib');
            $table->text('alur_kerja')->nullable()->after('fitur_tambahan');
            $table->text('kebutuhan_integrasi')->nullable()->after('alur_kerja');
            $table->text('referensi_desain')->nullable()->after('kebutuhan_integrasi');
            $table->string('kesiapan_aset')->nullable()->default('Belum Siap Sama Sekali')->after('referensi_desain');
            $table->string('target_waktu')->nullable()->after('kesiapan_aset');
            $table->boolean('is_published')->default(false)->after('project_status');
            $table->json('prd_content')->nullable()->after('is_published');
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
