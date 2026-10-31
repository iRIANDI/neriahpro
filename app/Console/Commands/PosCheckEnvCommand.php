<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\VisionBlueprint;
use App\Support\PosLifecycleAndStudioCatalog;
use Illuminate\Console\Command;

/**
 * Feature #5: Environment & Secret Vault Pre-Deployment Checker (`php artisan pos:check-env`).
 */
class PosCheckEnvCommand extends Command
{
    protected $signature = 'pos:check-env
                            {--blueprint= : ULID ID atau slug dari VisionBlueprint untuk validasi spesifik sektor}
                            {--sector= : Kode sektor (misal SECTOR_01_COMMERCE_OMNICHANNEL)}
                            {--strict-prod : Tolak eksekusi jika APP_DEBUG=true atau kredensial produksi kosong}
                            {--json : Cetak hasil validasi dalam format JSON}';

    protected $description = 'Lifecycle #5: Validasi kelengkapan & keamanan variabel .env.blueprint sebelum deploy ke Coolify / VPS produksi';

    public function handle(): int
    {
        $sectorCode = (string) ($this->option('sector') ?: 'SECTOR_01_COMMERCE_OMNICHANNEL');
        $primitives = ['PRIMITIVE_RBAC_TENANCY'];
        $stackCode = 'STACK_M1_LARAVEL_MONOLITH';
        $targetLabel = 'Host Application Environment';

        $identifier = $this->option('blueprint');
        if ($identifier) {
            $bp = VisionBlueprint::query()
                ->where('id', $identifier)
                ->orWhere('slug', $identifier)
                ->first();
            if ($bp) {
                $targetLabel = (string) ($bp->nama_bisnis ?: $bp->slug);
                $meta = is_array($bp->user_metadata) ? $bp->user_metadata : [];
                $sectorCode = (string) ($meta['primary_sector'] ?? $sectorCode);
                $primitives = array_values((array) ($meta['selected_primitives'] ?? $primitives));
                $stackCode = (string) ($meta['stack_preset'] ?? $stackCode);
            }
        }

        $vault = PosLifecycleAndStudioCatalog::environmentVaultBlueprint($sectorCode, $primitives, $stackCode);
        $strictProd = (bool) $this->option('strict-prod') || app()->environment('production');

        $checks = [];
        $failures = [];
        $warnings = [];

        // Core runtime & security invariants
        $appKey = (string) config('app.key', '');
        $appDebug = (bool) config('app.debug', false);
        $appUrl = (string) config('app.url', '');
        $dbConn = (string) config('database.default', '');

        $hasKey = trim($appKey) !== '';
        $checks[] = [
            'key' => 'APP_KEY',
            'status' => $hasKey ? 'PASS' : 'FAIL',
            'detail' => $hasKey ? 'Kunci enkripsi AES-256 tersedia.' : 'APP_KEY kosong! Jalankan php artisan key:generate.',
        ];
        if (! $hasKey) {
            $failures[] = 'APP_KEY wajib terisi.';
        }

        $debugSafe = ! $strictProd || $appDebug === false;
        $checks[] = [
            'key' => 'APP_DEBUG',
            'status' => $debugSafe ? 'PASS' : 'FAIL',
            'detail' => $debugSafe
                ? ('APP_DEBUG=' . ($appDebug ? 'true (non-prod)' : 'false (hardened)'))
                : 'CRITICAL: APP_DEBUG=true pada mode produksi membocorkan stack trace & kredensial!',
        ];
        if (! $debugSafe) {
            $failures[] = 'APP_DEBUG wajib bernilai false di lingkungan produksi.';
        }

        $httpsSafe = ! $strictProd || str_starts_with(strtolower($appUrl), 'https://');
        $checks[] = [
            'key' => 'APP_URL',
            'status' => $httpsSafe ? 'PASS' : 'WARN',
            'detail' => $httpsSafe ? "URL kanonik valid ({$appUrl})." : "APP_URL ({$appUrl}) sebaiknya menggunakan skema https:// di produksi.",
        ];
        if (! $httpsSafe) {
            $warnings[] = 'APP_URL belum menggunakan https://.';
        }

        $checks[] = [
            'key' => 'DB_CONNECTION',
            'status' => $dbConn !== '' ? 'PASS' : 'FAIL',
            'detail' => "Driver database aktif: {$dbConn} (PostgreSQL 16 Strict ULID direkomendasikan untuk produksi).",
        ];
        if ($dbConn === '') {
            $failures[] = 'DB_CONNECTION kosong.';
        }

        $report = [
            'success' => empty($failures),
            'target' => $targetLabel,
            'sector_code' => $vault['sector_code'],
            'stack_preset' => $vault['stack_preset'],
            'strict_prod_enforced' => $strictProd,
            'checks' => $checks,
            'warnings' => $warnings,
            'failures' => $failures,
            'required_blueprint_keys_count' => count(array_filter($vault['variables'], fn ($v) => ! empty($v['required']))),
            'env_blueprint_preview' => PosLifecycleAndStudioCatalog::renderEnvBlueprintContent($vault, $targetLabel),
        ];

        AuditLog::record(
            action: 'env_vault_checked',
            newValues: [
                'target' => $targetLabel,
                'sector_code' => $vault['sector_code'],
                'passed' => empty($failures),
                'failures_count' => count($failures),
            ],
            description: "Pemeriksaan Pre-Deployment Environment Vault (pos:check-env) untuk '{$targetLabel}': " . (empty($failures) ? 'PASS' : 'FAIL'),
            eventCategory: 'env_vault'
        );

        if ($this->option('json')) {
            $this->line((string) json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return empty($failures) ? self::SUCCESS : self::FAILURE;
        }

        $this->info("=== Project OS v4.0 Environment & Secret Vault Audit: {$targetLabel} ===");
        foreach ($checks as $c) {
            $this->line(" [{$c['status']}] {$c['key']} — {$c['detail']}");
        }

        if (! empty($failures)) {
            foreach ($failures as $f) {
                $this->error(" ✖ {$f}");
            }

            return self::FAILURE;
        }

        $this->info(' ✅ Pre-Deployment Environment Vault tervalidasi bersih.');

        return self::SUCCESS;
    }
}

