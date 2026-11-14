<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use App\Support\WorldDesignSystemCatalog;
use Database\Seeders\ProjectOsMatrixSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ScaffoldHardeningAndDesignSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => Http::response([
                'embedding' => ['values' => array_fill(0, 768, 0.01)],
                'candidates' => [['content' => ['parts' => [['text' => '{}']]]]],
            ], 200),
        ]);

        (new ProjectOsMatrixSeeder())->run();
    }

    public function test_portable_dockerfile_railpack_zero_nixpacks_and_runnable_laravel_13_skeleton(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Operations Director',
            'email' => 'ops@example.test',
            'whatsapp' => '628111222333',
            'nama_bisnis' => 'Unified Audio & Dispatch Suite',
            'slug' => 'unified-audio-dispatch',
            'tipe_aplikasi' => 'Web App & Realtime Audio',
            'target_pengguna' => 'Operators & Listeners',
            'masalah_utama' => 'Live multi-channel WebRTC audio broadcast and event room management',
            'solusi_diinginkan' => 'WebRTC audio streams via LiveKit and Laravel Reverb real-time rooms',
            'kebutuhan_integrasi' => 'LiveKit, Laravel Reverb, S3 Storage',
            'fitur_wajib' => "Audio Channels (flag_icon, active/inactive, assign default_translator)\nBroadcast Rooms (room_code, bitrate_kbps, status)",
            'project_status' => 'Retail License',
            'is_published' => true,
            'user_metadata' => [
                'retail_tier' => 'retail_ultimate',
                'paid' => true,
                'package_tier' => 'retail_ultimate',
                'infra_config' => [
                    'db_database' => 'ops_custom_db',
                    'db_username' => 'ops_pg_admin',
                    'db_password' => 'CustomPass#2026',
                    'db_port' => 5433,
                    'app_port' => 8095,
                    'app_url' => 'http://192.168.10.20:8095',
                    'redis_password' => 'RedisPass!99',
                    'admin_seed_name' => 'Root Custodian',
                    'admin_seed_email' => 'custodian@workspace.test',
                    'admin_seed_password' => 'RootSeed!2026',
                    'livekit_api_key' => 'lk_custom_key_01',
                    'livekit_lan_host' => '192.168.10.20',
                ],
            ],
        ]);

        $files = ScaffoldGeneratorService::generateFiles($blueprint);

        // 1. Zero Nixpacks anywhere in generated scaffold
        $this->assertArrayNotHasKey('nixpacks.toml', $files);
        foreach ($files as $path => $content) {
            $this->assertFalse(str_contains(strtolower($path), 'nixpack'), "Found unexpected Nixpacks file: {$path}");
            if (is_string($content)) {
                $this->assertFalse(
                    str_contains(strtolower($content), 'nixpack'),
                    "Found Nixpacks reference inside generated file: {$path}"
                );
                $this->assertDoesNotMatchRegularExpression(
                    '/\{\{\s*[A-Z][A-Z0-9_]{2,}\s*\}\}/',
                    $content,
                    "Found unreplaced stub placeholder in {$path}"
                );
            }
        }

        // 2. Valid Railpack v2 configuration (PHP 8.5 + Node 22 + FrankenPHP)
        $this->assertArrayHasKey('railpack.json', $files);
        $railpack = json_decode($files['railpack.json'], true);
        $this->assertIsArray($railpack);
        $this->assertSame('php', $railpack['provider'] ?? null);
        $this->assertSame('8.5', $railpack['packages']['php'] ?? null);
        $this->assertSame('22', $railpack['packages']['node'] ?? null);
        $this->assertStringContainsString('frankenphp', $railpack['deploy']['startCommand'] ?? '');

        // 3. Dockerfile.portable multi-stage order: composer-vendor BEFORE node asset build
        $this->assertArrayHasKey('Dockerfile.portable', $files);
        $dockerfile = $files['Dockerfile.portable'];
        $composerPos = strpos($dockerfile, 'AS composer-vendor');
        $assetsPos = strpos($dockerfile, 'AS assets');
        $copyVendorInAssetsPos = strpos($dockerfile, 'COPY --from=composer-vendor /app/vendor ./vendor');
        $npmBuildPos = strpos($dockerfile, 'npm run build');
        $appStagePos = strpos($dockerfile, 'AS app');
        $webStagePos = strpos($dockerfile, 'AS web');

        $this->assertNotFalse($composerPos);
        $this->assertNotFalse($assetsPos);
        $this->assertNotFalse($copyVendorInAssetsPos);
        $this->assertNotFalse($npmBuildPos);
        $this->assertNotFalse($appStagePos);
        $this->assertNotFalse($webStagePos);
        $this->assertLessThan($assetsPos, $composerPos);
        $this->assertGreaterThan($assetsPos, $copyVendorInAssetsPos);
        $this->assertLessThan($npmBuildPos, $copyVendorInAssetsPos);
        $this->assertLessThan($appStagePos, $npmBuildPos);
        $this->assertLessThan($webStagePos, $appStagePos);

        // 4. Dynamic Infrastructure Credentials propagated across env, compose, backup/restore, seeder
        $this->assertStringContainsString('DB_DATABASE=ops_custom_db', $files['.env']);
        $this->assertStringContainsString('DB_USERNAME=ops_pg_admin', $files['.env']);
        $this->assertStringContainsString('DB_PASSWORD=CustomPass#2026', $files['.env']);
        $this->assertStringContainsString('APP_PORT=8095', $files['.env']);
        $this->assertStringContainsString('REDIS_PASSWORD=RedisPass!99', $files['.env']);
        $this->assertStringContainsString('DB_DATABASE=ops_custom_db', $files['.env.example']);

        $this->assertStringContainsString('ops_custom_db', $files['docker-compose.yml']);
        $this->assertStringContainsString('ops_pg_admin', $files['docker-compose.portable.yml']);
        $this->assertStringContainsString('ops_custom_db', $files['scripts/backup.sh']);
        $this->assertStringContainsString('ops_custom_db', $files['scripts/restore.sh']);
        $this->assertStringContainsString('custodian@workspace.test', $files['database/seeders/SyntheticDataSeeder.php']);
        $this->assertStringContainsString('Root Custodian', $files['database/seeders/SyntheticDataSeeder.php']);

        // 5. Conditional LAN WebRTC LiveKit & Reverb files
        $this->assertArrayHasKey('livekit.yaml', $files);
        $this->assertStringContainsString('stun_servers: []', $files['livekit.yaml']);
        $this->assertStringContainsString('lk_custom_key_01', $files['livekit.yaml']);
        $this->assertArrayHasKey('resources/js/realtime/live-stage-client.js', $files);
        $this->assertStringContainsString('window.location.hostname', $files['resources/js/realtime/live-stage-client.js']);

        // 6. Full runnable Laravel 13 skeleton & DB-safe phpunit.xml
        foreach ([
            'composer.json',
            'package.json',
            'vite.config.js',
            'artisan',
            'public/index.php',
            'public/openapi.json',
            'bootstrap/app.php',
            'bootstrap/providers.php',
            'app/Providers/AppServiceProvider.php',
            'config/app.php',
            'config/database.php',
            'app/Models/User.php',
            'app/Traits/ScopesToTenantOrganization.php',
            'app/Http/Middleware/AiThreatShield.php',
            'app/Http/Middleware/EnsureOperatorRole.php',
            'routes/auth.php',
            'routes/console.php',
            'phpunit.xml',
            'tests/TestCase.php',
            'resources/css/app.css',
            'resources/js/app.js',
            'DESIGN-SYSTEM.md',
        ] as $skeletonFile) {
            $this->assertArrayHasKey($skeletonFile, $files, "Missing Laravel skeleton file: {$skeletonFile}");
        }

        $this->assertStringContainsString('ops_custom_db_testing', $files['phpunit.xml']);
        $this->assertStringContainsString('_testing', $files['tests/TestCase.php']);

        // 7. PHPUnit 12 Test Classes (Zero Pest closures)
        $this->assertStringContainsString('extends TestCase', $files['tests/Feature/DeterministicStateMachineContractTest.php']);
        $this->assertStringNotContainsString("test('", $files['tests/Feature/DeterministicStateMachineContractTest.php']);
        $this->assertStringContainsString('extends TestCase', $files['tests/Feature/ZeroAssumptionAcceptanceMatrixTest.php']);
        $this->assertStringNotContainsString("test('", $files['tests/Feature/ZeroAssumptionAcceptanceMatrixTest.php']);

        // 8. Clean Routes, Models, DDL (no Sanctum middleware dependency, ->parameters([$t => 'record']), User::class FK, 19,4 decimals)
        $this->assertStringNotContainsString('auth:sanctum', $files['routes/api.php']);
        $this->assertStringContainsString("'operator.role:", $files['routes/api.php']);
        $this->assertStringContainsString("->parameters(['audio_channels' => 'record'])", $files['routes/api.php']);
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $files['resources/views/welcome.blade.php']);
        $this->assertStringNotContainsString('@scalar/api-reference', $files['resources/views/api-docs.blade.php']);
        $this->assertStringContainsString('@vite([\'resources/css/app.css\', \'resources/js/app.js\'])', $files['resources/views/welcome.blade.php']);
        $this->assertStringContainsString('User::class', $files['app/Models/AudioChannel.php']);
        $this->assertStringContainsString('ScopesToTenantOrganization', $files['app/Models/AudioChannel.php']);
        $this->assertStringNotContainsString('DefaultTranslator::class', $files['app/Models/AudioChannel.php']);
    }

    public function test_ten_worldwide_design_systems_calibrate_per_domain_and_word_boundary_matches_safely(): void
    {
        $catalog = WorldDesignSystemCatalog::catalogForUi();
        $this->assertCount(10, $catalog);

        // Clinical healthcare blueprint auto-selects CLINICAL_MODULAR_HUD
        $clinicBp = new VisionBlueprint([
            'nama_bisnis' => 'Metro Regional Clinical EMR',
            'slug' => 'metro-clinical-emr',
            'masalah_utama' => 'Hospital outpatient triage, ICD-10 medical record tracking, and inpatient bed dispatch',
            'solusi_diinginkan' => 'Clinical EHR and pharmacy queue cockpit',
        ]);
        $clinicResolved = WorldDesignSystemCatalog::resolveForBlueprint($clinicBp);
        $this->assertSame('CLINICAL_STERILE_PRECISION', $clinicResolved['code']);

        // Explicit design system override (ARCHITECTURAL_EDITORIAL_SERIF) changes generated app.css & welcome.blade
        $editorialBp = VisionBlueprint::create([
            'client_name' => 'Editor Lead',
            'email' => 'editor@example.test',
            'whatsapp' => '628122334455',
            'nama_bisnis' => 'Multi-Position Fleet & Warehouse Ledger',
            'slug' => 'multi-position-fleet',
            'masalah_utama' => 'Multi-position cargo routing without retail cashiers',
            'solusi_diinginkan' => 'Logistics manifest and financial settlement ERP',
            'is_published' => true,
            'user_metadata' => [
                'design_system' => 'ARCHITECTURAL_EDITORIAL_SERIF',
            ],
        ]);

        // Word-boundary check: "multi-position" must NOT falsely activate Retail POS archetype
        $compiled = ProjectOsBlueprintCompilerService::compileUniversalSchema($editorialBp);
        $archetypes = collect($compiled['ui_screen_archetypes'] ?? [])->pluck('archetype')->implode(',');
        $this->assertStringNotContainsString('SPLIT_POS', strtoupper($archetypes));

        $editorialFiles = ScaffoldGeneratorService::generateFiles($editorialBp);
        $this->assertArrayNotHasKey('livekit.yaml', $editorialFiles);
        $this->assertStringContainsString('ARCHITECTURAL_EDITORIAL_SERIF', $editorialFiles['DESIGN-SYSTEM.md']);
        $this->assertStringContainsString('ARCHITECTURAL_EDITORIAL_SERIF', $editorialFiles['resources/css/app.css']);
        $this->assertStringNotContainsString('bg-gradient-to-', $editorialFiles['resources/views/welcome.blade.php']);
        $this->assertStringNotContainsString('rounded-full', $editorialFiles['resources/views/welcome.blade.php']);
    }

    public function test_save_infra_config_endpoint_validates_persists_invalidates_zip_and_hydrates_preview(): void
    {
        $owner = User::factory()->create(['email' => 'architect@example.test']);
        $otherUser = User::factory()->create(['email' => 'intruder@example.test']);

        $blueprint = VisionBlueprint::create([
            'client_name' => 'Owner Architect',
            'email' => 'architect@example.test',
            'whatsapp' => '628199887766',
            'nama_bisnis' => 'Custom Cloud Ledger',
            'slug' => 'custom-cloud-ledger',
            'masalah_utama' => 'General ledger reconciliation',
            'solusi_diinginkan' => 'Double-entry ERP ledger',
            'project_status' => 'Retail License',
            'is_published' => false,
            'user_metadata' => [
                'retail_tier' => 'retail_ultimate',
                'paid' => true,
                'package_tier' => 'retail_ultimate',
                'scaffold_zip_path' => 'scaffolds/stale-archive.zip',
                'scaffold_ready' => true,
            ],
        ]);

        // Non-owner must be denied
        $this->actingAs($otherUser)
            ->postJson(route('blueprint.scaffold.save-infra-config', $blueprint->slug), [
                'db_database' => 'hijacked_db',
            ])
            ->assertStatus(403);

        // Unknown design system rejected with 422
        $this->actingAs($owner)
            ->postJson(route('blueprint.scaffold.save-infra-config', $blueprint->slug), [
                'design_system' => 'NON_EXISTENT_THEME',
            ])
            ->assertStatus(422);

        // Owner valid update persists & invalidates stale ZIP cache
        $res = $this->actingAs($owner)
            ->postJson(route('blueprint.scaffold.save-infra-config', $blueprint->slug), [
                'design_system' => 'FIDUCIARY_QUANT_TERMINAL',
                'db_database' => 'ledger_prod_db',
                'db_username' => 'ledger_ops',
                'db_password' => 'LedgerStrong!26',
                'db_port' => 5435,
                'app_port' => 8123,
                'app_url' => 'https://ledger.internal.test',
                'admin_seed_email' => 'controller@ledger.test',
            ]);

        $res->assertOk()
            ->assertJson([
                'success' => true,
                'infra_config' => [
                    'db_database' => 'ledger_prod_db',
                    'db_username' => 'ledger_ops',
                    'db_port' => 5435,
                    'app_port' => 8123,
                ],
                'design_system' => [
                    'selected' => 'FIDUCIARY_QUANT_TERMINAL',
                    'effective_code' => 'FIDUCIARY_QUANT_TERMINAL',
                ],
            ]);

        $blueprint->refresh();
        $this->assertArrayNotHasKey('scaffold_zip_path', $blueprint->user_metadata ?? []);
        $this->assertFalse($blueprint->user_metadata['scaffold_ready'] ?? true);

        // Preview endpoint returns updated credentials & theme inside generated files
        $preview = $this->actingAs($owner)
            ->getJson(route('blueprint.scaffold.preview', $blueprint->slug))
            ->assertOk()
            ->json();

        $this->assertSame('FIDUCIARY_QUANT_TERMINAL', $preview['design_system']['effective_code'] ?? null);
        $this->assertSame('ledger_prod_db', $preview['infra_config']['db_database'] ?? null);
        $this->assertStringContainsString('DB_DATABASE=ledger_prod_db', $preview['files']['.env']);
        $this->assertStringContainsString('FIDUCIARY_QUANT_TERMINAL', $preview['files']['resources/css/app.css']);
    }
}
