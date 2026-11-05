<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\VisionBlueprint;
use Illuminate\Console\Command;

class BlueprintVipBypassCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blueprint:vip-bypass {slug : Slug atau ID dari Vision Blueprint}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '1-Click VIP Owner Bypass: Aktifkan blueprint ke status Active Sprint & buka seluruh 15 Pilar Project OS tanpa pembayaran.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $slug = $this->argument('slug');
        $this->info("👑 Menjalankan VIP Owner Bypass untuk: {$slug}...");

        $blueprint = VisionBlueprint::where('slug', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (! $blueprint) {
            $this->error("❌ Blueprint dengan slug/ID '{$slug}' tidak ditemukan.");
            return 1;
        }

        // Record digital sign-off and SHA-256 audit hash
        $blueprint->recordSignOff('127.0.0.1 (CLI)', 'Artisan Console');

        // Provision staging demo sandbox URL
        $stagingUrl = $blueprint->provisionStagingUrl();

        // Update blueprint status to Active Sprint with VIP bypass
        $blueprint->update([
            'voucher_code' => 'VIP-OWNER-BYPASS',
            'is_free_grant' => true,
            'is_published' => true,
            'project_status' => 'Active Sprint',
            'signed_agreement' => true,
        ]);

        // Resolve Super Admin email dynamically
        $superAdminEmail = \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->value('email')
            ?? (\App\Models\User::firstWhere('email', '!=', null)?->email ?? config('mail.from.address'));

        // Interconnect with Document/Contract with explicit signer metadata
        Document::updateOrCreate(
            [
                'related_type' => VisionBlueprint::class,
                'related_id' => $blueprint->id,
                'document_type' => 'contract',
            ],
            [
                'title' => 'Kontrak VIP Founder OS - ' . ($blueprint->nama_bisnis ?: $blueprint->client_name),
                'status' => 'signed',
                'scope_locked' => true,
                'contract_amount' => 0.00,
                'dp_amount' => 0.00,
                'signed_at' => now(),
                'signer_name' => 'Solo Founder (Super Admin CLI)',
                'signer_email' => $superAdminEmail,
                'signer_ip_address' => '127.0.0.1 (CLI)',
                'document_hash' => $blueprint->document_sha256 ?: $blueprint->calculatePrdHash(),
            ]
        );

        $this->info("✅ VIP Owner Bypass Berhasil!");
        $this->line("   - Proyek: " . ($blueprint->nama_bisnis ?: $blueprint->client_name));
        $this->line("   - Status: Active Sprint (15 Pilar Terbuka Penuh)");
        $this->line("   - Staging Sandbox: {$stagingUrl}");
        $this->line("   - URL Blueprint: " . route('blueprint.show', $blueprint->slug));

        return 0;
    }
}

