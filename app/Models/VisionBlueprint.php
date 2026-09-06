<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class VisionBlueprint extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'slug',
        'client_name',
        'nama_bisnis',
        'email',
        'phone',
        'service_options',
        'project_status',
        'is_published',
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
        'prd_content',
        'ip_address',
        'user_metadata',
    ];

    protected $casts = [
        'service_options' => 'array',
        'user_metadata' => 'array',
        'prd_content' => 'array',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $base = $model->nama_bisnis ?: $model->client_name;
                $model->slug = Str::slug($base) . '-' . strtolower(Str::random(5));
            }
        });
    }

    /**
     * Polymorphic relation to generated documents / contracts.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'related');
    }

    /**
     * Generate or regenerate the Ultimate PRD for this blueprint.
     */
    public function generateAndSavePrd(): array
    {
        $content = \App\Services\PrdGeneratorService::generate($this);
        $this->update(['prd_content' => $content]);
        return $content;
    }

    /**
     * Interconnection: Convert this Project OS PRD into an official Digital Contract.
     */
    public function convertToDigitalContract(array $overrides = []): Document
    {
        $contractAmount = $overrides['contract_amount'] ?? 50000000.00;
        $dpAmount = $overrides['dp_amount'] ?? ($contractAmount * 0.50);

        $clauses = [
            'pasal_1_ruang_lingkup' => [
                'title' => 'Pasal 1: Ruang Lingkup Sistem & Spesifikasi PRD',
                'description' => 'Pihak Kedua sepakat untuk mengembangkan arsitektur sistem perangkat lunak untuk "' . ($this->nama_bisnis ?: $this->client_name) . '" sesuai dengan rincian fitur MVP Fase 1 yang tercantum dalam Ultimate PRD (ID: ' . strtoupper(substr($this->id, 0, 10)) . ').',
            ],
            'pasal_2_timeline_sprint' => [
                'title' => 'Pasal 2: Alokasi Waktu Pengerjaan (5 Sprint Kerja)',
                'description' => 'Pekerjaan dilaksanakan dengan total durasi ' . ($this->target_waktu ?: '30 Hari Kerja') . ' yang dibagi ke dalam 5 Sprint berurutan (Sprint 1: Architecture, Sprint 2: Core MVP, Sprint 3: Frontend Flow, Sprint 4: Security Audit, Sprint 5: Deployment VPS).',
            ],
            'pasal_3_biaya_dan_dp' => [
                'title' => 'Pasal 3: Nilai Kontrak & Ketentuan Pembayaran DP',
                'description' => 'Total nilai investasi proyek adalah Rp ' . number_format($contractAmount, 0, ',', '.') . ' dengan termin pembayaran: Uang Muka (DP 50%) sebesar Rp ' . number_format($dpAmount, 0, ',', '.') . ' dibayarkan sebelum pekerjaan dimulai, dan Pelunasan (50%) saat serah terima sistem.',
            ],
            'pasal_4_penguncian_scope' => [
                'title' => 'Pasal 4: Penguncian Ruang Lingkup (Scope Freeze)',
                'description' => 'Seluruh fitur di luar spesifikasi PRD ini dinyatakan sebagai ruang lingkup baru yang akan diakomodasikan melalui Addendum / Change Request (CR) terpisah dengan biaya dan tambahan hari kerja tersendiri tanpa mengubah tanggal kontrak utama.',
            ],
            'pasal_5_keabsahan_hukum' => [
                'title' => 'Pasal 5: Tanda Tangan Elektronik & Integritas Dokumen',
                'description' => 'Surat perjanjian ini sah dan berkekuatan hukum tetap, ditandatangani secara digital dengan pencatatan audit trail IP address, timestamp, dan enkripsi cryptographic hash SHA-256.',
            ],
        ];

        $document = Document::create([
            'title' => 'Perjanjian Kerja Sama Pengembangan Sistem - ' . ($this->nama_bisnis ?: $this->client_name),
            'document_type' => 'contract',
            'related_type' => self::class,
            'related_id' => $this->id,
            'status' => $overrides['status'] ?? 'pending_signature',
            'scope_locked' => true,
            'contract_amount' => $contractAmount,
            'dp_amount' => $dpAmount,
            'midtrans_order_id' => $overrides['midtrans_order_id'] ?? ('NPRO-DP-' . strtoupper(Str::random(8))),
            'midtrans_payment_url' => $overrides['midtrans_payment_url'] ?? 'https://app.sandbox.midtrans.com/snap/v2/vtweb/demo-neriahpro-dp',
            'signer_name' => $this->client_name ?: $this->nama_bisnis,
            'signer_email' => $this->email,
            'signer_ip_address' => $overrides['signer_ip_address'] ?? null,
            'signed_at' => $overrides['signed_at'] ?? null,
            'digital_signature_image' => $overrides['digital_signature_image'] ?? null,
            'document_hash' => $overrides['document_hash'] ?? null,
            'content_clauses' => $clauses,
        ]);

        $this->update([
            'project_status' => $overrides['project_status'] ?? 'Contract Created',
        ]);

        return $document;
    }

    /**
     * Check if PRD is published to the client.
     */
    public function isPublic(): bool
    {
        return (bool) $this->is_published;
    }

    /**
     * Get public shareable link.
     */
    public function getPublicUrlAttribute(): string
    {
        return url('/blueprint/' . $this->slug);
    }
}
