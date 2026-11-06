<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\PosCommercialBridgeService;
use Illuminate\Console\Command;

class BlueprintUnlockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blueprint:unlock {slug : Slug atau ID dari Vision Blueprint} {--reason= : Alasan pembukaan kunci scope}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '1-Click Unlock Scope: Buka kunci scope blueprint agar bebas elaborasi arsitektur dan spesifikasi kembali.';

    /**
     * Execute the console command.
     */
    public function handle(PosCommercialBridgeService $bridge): int
    {
        $slug = $this->argument('slug');
        $reason = (string) ($this->option('reason') ?: 'Unlocked via Super Admin CLI');

        $this->info("🔓 Membuka kunci scope blueprint: {$slug}...");

        $blueprint = VisionBlueprint::where('slug', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (! $blueprint) {
            $this->error("❌ Blueprint dengan slug/ID '{$slug}' tidak ditemukan.");
            return 1;
        }

        // Cari super admin pertama sebagai aktor audit
        $superAdmin = User::whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->first()
            ?? User::first();

        $result = $bridge->unlockScope($blueprint, $superAdmin, $reason);

        if (! $result['success']) {
            $this->error("❌ Gagal membuka kunci: {$result['message']}");
            return 1;
        }

        $this->info("✅ Sukses: {$result['message']}");
        $this->line("   - Status Baru: {$result['blueprint']->project_status}");
        $this->line("   - URL: " . url("/blueprint/{$result['blueprint']->slug}"));

        return 0;
    }
}

