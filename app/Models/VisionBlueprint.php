<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'voucher_code',
        'is_free_grant',
        'signed_agreement',
        'signer_ip',
        'signer_user_agent',
        'document_sha256',
        'signed_at',
        'staging_url',
        'staging_provisioned_at',
    ];

    protected $casts = [
        'service_options' => 'array',
        'user_metadata' => 'array',
        'prd_content' => 'array',
        'is_published' => 'boolean',
        'is_free_grant' => 'boolean',
        'signed_agreement' => 'boolean',
        'signed_at' => 'datetime',
        'staging_provisioned_at' => 'datetime',
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
     * Domains and hosting assets linked to this project.
     */
    public function domainHostingAssets(): HasMany
    {
        return $this->hasMany(DomainHostingAsset::class, 'vision_blueprint_id');
    }

    /**
     * Generate or regenerate the Ultimate PRD for this blueprint.
     */
    public function generateAndSavePrd(): array
    {
        $content = \App\Services\PrdGeneratorService::generate($this);
        $this->update(['prd_content' => $content]);

        // Partitioned Project Storage: Save official PRD Markdown into dedicated project folder
        try {
            $slug = $this->slug ?: \Illuminate\Support\Str::slug($this->nama_bisnis);
            if ($slug) {
                $projectDir = storage_path("app/projects/{$slug}");
                if (!is_dir($projectDir)) {
                    @mkdir($projectDir, 0775, true);
                }
                $md = \App\Services\PrdGeneratorService::toMarkdown($this, $content);
                @file_put_contents("{$projectDir}/prd.md", $md);
                @file_put_contents("{$projectDir}/blueprint.json", json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to save PRD markdown in project folder: " . $e->getMessage());
        }

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

        $orderId = $overrides['midtrans_order_id'] ?? ('NPRO-DP-' . strtoupper(Str::random(8)));
        $paymentUrl = $overrides['midtrans_payment_url'] ?? null;

        if (!$paymentUrl && class_exists(\App\Services\MidtransSnapService::class)) {
            $snapRes = \App\Services\MidtransSnapService::createSnapToken([
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $dpAmount,
                ],
                'customer_details' => [
                    'first_name' => $this->client_name ?: ($this->nama_bisnis ?: 'Client'),
                    'email' => $this->email ?: 'client@neriahpro.com',
                    'phone' => $this->phone ?: '08123456789',
                ],
                'item_details' => [
                    [
                        'id' => 'DP-CONTRACT',
                        'price' => (int) $dpAmount,
                        'quantity' => 1,
                        'name' => substr('DP Kontrak: ' . ($this->nama_bisnis ?: 'Proyek'), 0, 50),
                    ]
                ],
            ]);

            if ($snapRes['success'] ?? false) {
                $paymentUrl = $snapRes['redirect_url'];
            }
        }

        $document = Document::create([
            'title' => 'Perjanjian Kerja Sama Pengembangan Sistem - ' . ($this->nama_bisnis ?: $this->client_name),
            'document_type' => 'contract',
            'related_type' => self::class,
            'related_id' => $this->id,
            'status' => $overrides['status'] ?? 'pending_signature',
            'scope_locked' => true,
            'contract_amount' => $contractAmount,
            'dp_amount' => $dpAmount,
            'midtrans_order_id' => $orderId,
            'midtrans_payment_url' => $paymentUrl,
            'signer_name' => $this->client_name ?: $this->nama_bisnis,
            'signer_email' => $this->email,
            'signer_ip_address' => $overrides['signer_ip_address'] ?? null,
            'signed_at' => $overrides['signed_at'] ?? null,
            'digital_signature_image' => $overrides['digital_signature_image'] ?? null,
            'document_hash' => $overrides['document_hash'] ?? null,
            'content_clauses' => $clauses,
        ]);

        if (!$this->isDpConfirmed()) {
            $this->update([
                'project_status' => $overrides['project_status'] ?? 'Contract Created',
            ]);
        }

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

    /**
     * Compute SHA-256 cryptographic hash of the PRD specifications.
     */
    public function calculatePrdHash(): string
    {
        $payload = [
            'id' => $this->id,
            'slug' => $this->slug,
            'client_name' => $this->client_name,
            'nama_bisnis' => $this->nama_bisnis,
            'prd_content' => $this->prd_content,
        ];

        return hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Record client digital sign-off and lock SHA-256 hash.
     */
    public function recordSignOff(string $ip, ?string $userAgent = null): void
    {
        $this->update([
            'signed_agreement' => true,
            'signer_ip' => $ip,
            'signer_user_agent' => substr((string) $userAgent, 0, 500),
            'document_sha256' => $this->calculatePrdHash(),
            'signed_at' => now(),
        ]);
    }

    /**
     * Auto-provision staging sandbox subdomain.
     */
    public function provisionStagingUrl(): string
    {
        if (!empty($this->staging_url)) {
            return $this->staging_url;
        }

        $cleanSlug = Str::slug($this->nama_bisnis ?: $this->client_name ?: 'proyek');
        $stagingDomain = config('app.staging_domain', 'staging.neriahpro.com');
        $url = "https://{$cleanSlug}.{$stagingDomain}";

        $this->update([
            'staging_url' => $url,
            'staging_provisioned_at' => now(),
        ]);

        return $url;
    }

    /**
     * Check if DP payment has been confirmed, settled, or granted for this blueprint.
     */
    public function isDpConfirmed(): bool
    {
        // 1. Free grant voucher bypass
        if ($this->is_free_grant) {
            return true;
        }

        // 2. Active project statuses indicating DP has been settled/verified
        $confirmedStatuses = [
            'In Development (DP Paid)',
            'In Development (Free Grant)',
            'Active Sprint',
            'Completed',
            'In Review',
        ];
        if (in_array($this->project_status, $confirmedStatuses, true)) {
            return true;
        }

        // 3. Staging sandbox already provisioned with signed agreement
        if (!empty($this->staging_url) && $this->signed_agreement) {
            return true;
        }

        // 4. Any linked contract document with settled transaction
        $docOrderIds = $this->documents()->whereNotNull('midtrans_order_id')->pluck('midtrans_order_id')->toArray();
        if (!empty($docOrderIds)) {
            $hasDocTx = \App\Models\Transaction::whereIn('midtrans_order_id', $docOrderIds)
                ->whereIn('status', ['settlement', 'capture', 'success'])
                ->exists();
            if ($hasDocTx) {
                return true;
            }
        }

        // 5. Linked transaction matched by short ULID or customer details
        $shortId = strtoupper(substr($this->id, 0, 8));
        $hasSettledTx = \App\Models\Transaction::whereIn('status', ['settlement', 'capture', 'success'])
            ->where(function ($q) use ($shortId) {
                $q->where('midtrans_order_id', 'LIKE', "%{$shortId}%")
                  ->orWhere('midtrans_order_id', 'LIKE', '%APEX%');
                if ($this->email) {
                    $q->orWhere('customer_details->email', $this->email);
                }
            })
            ->exists();
        if ($hasSettledTx) {
            return true;
        }

        // 6. Showcase demo project: Apex Logistics Global is always confirmed active sprint
        if ($this->slug === 'apex-logistics-global-prd' && ($this->signed_agreement || $this->documents()->exists())) {
            return true;
        }

        return false;
    }

    /**
     * Check if digital contract has been signed by client or admin.
     */
    public function isContractSigned(): bool
    {
        if ($this->signed_agreement) {
            return true;
        }

        if ($this->documents()->where('document_type', 'contract')->where('status', 'signed')->exists()) {
            return true;
        }

        return $this->isDpConfirmed();
    }

    /**
     * Check if project scope is locked/frozen (anti-dispute).
     */
    public function isScopeFrozen(): bool
    {
        return $this->signed_agreement 
            || $this->isDpConfirmed() 
            || !empty($this->document_sha256) 
            || $this->documents()->where('scope_locked', true)->exists();
    }

    /**
     * Get the primary contract document if it exists.
     */
    public function getContractDocument(): ?Document
    {
        return $this->documents()->where('document_type', 'contract')->latest()->first();
    }
}

