<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PosPrimitive;
use App\Models\PosSector;
use App\Models\VisionBlueprint;
use App\Services\Prd\PrdArchitectureManifestoService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use Database\Seeders\ProjectOsMatrixSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProjectOsBiometricsAndGeofencePrimitiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => Http::response([
                'embedding' => ['values' => array_fill(0, 768, 0.0125)],
                'candidates' => [
                    ['content' => ['parts' => [['text' => '{}']]]],
                ],
            ], 200),
        ]);

        $this->seed(ProjectOsMatrixSeeder::class);
    }

    public function test_biometrics_and_geofencing_primitives_are_seeded_with_strict_ulid_and_schemas(): void
    {
        // 1. PRIMITIVE_AI_VISION_BIOMETRICS
        $bio = PosPrimitive::query()->where('code', 'PRIMITIVE_AI_VISION_BIOMETRICS')->firstOrFail();
        $this->assertSame(26, strlen($bio->id));
        $this->assertSame('AI_VISION', $bio->category);
        $this->assertNotEmpty($bio->schema_blueprints);
        $this->assertNotEmpty($bio->state_machines);
        $this->assertNotEmpty($bio->deterministic_formulas);
        $this->assertNotEmpty($bio->edge_case_playbooks);
        $this->assertNotEmpty($bio->gemini_tool_declarations);

        $tables = collect($bio->schema_blueprints)->pluck('table')->all();
        $this->assertContains('biometric_face_vectors', $tables);
        $this->assertContains('biometric_verification_logs', $tables);

        $stateMachines = collect($bio->state_machines)->pluck('entity')->all();
        $this->assertContains('BiometricVector', $stateMachines);

        // Verify UU PDP Right to Erasure action exists
        $transitions = $bio->state_machines[0]['transitions'] ?? [];
        $actions = collect($transitions)->pluck('action')->all();
        $this->assertContains('PurgeBiometricRightToErasureAction', $actions);

        // 2. PRIMITIVE_GEOSPATIAL_GEOFENCE
        $geo = PosPrimitive::query()->where('code', 'PRIMITIVE_GEOSPATIAL_GEOFENCE')->firstOrFail();
        $this->assertSame(26, strlen($geo->id));
        $this->assertSame('SPATIAL_LOCATION', $geo->category);
        $this->assertNotEmpty($geo->schema_blueprints);
        $this->assertNotEmpty($geo->state_machines);
        $this->assertNotEmpty($geo->deterministic_formulas);
        $this->assertNotEmpty($geo->edge_case_playbooks);
        $this->assertNotEmpty($geo->gemini_tool_declarations);

        $geoTables = collect($geo->schema_blueprints)->pluck('table')->all();
        $this->assertContains('geofence_zones', $geoTables);
        $this->assertContains('geofence_presence_logs', $geoTables);

        // Verify Out of Office Approval flow exists
        $geoTransitions = $geo->state_machines[0]['transitions'] ?? [];
        $geoActions = collect($geoTransitions)->pluck('action')->all();
        $this->assertContains('RouteToSupervisorOutOfOfficeApprovalAction', $geoActions);
        $this->assertContains('ApproveOutOfOfficePresenceAction', $geoActions);
    }

    public function test_sectors_dynamically_incorporate_biometrics_and_geofence_primitives(): void
    {
        // Sector 11 HRIS includes both
        $hris = PosSector::query()->where('code', 'SECTOR_11_HRIS_INTERNAL_OPS')->with('primitives')->firstOrFail();
        $hrisPrimCodes = $hris->primitives->pluck('code')->all();
        $this->assertContains('PRIMITIVE_AI_VISION_BIOMETRICS', $hrisPrimCodes);
        $this->assertContains('PRIMITIVE_GEOSPATIAL_GEOFENCE', $hrisPrimCodes);
        $this->assertContains('PRIMITIVE_HRIS_PAYROLL_ROSTER', $hrisPrimCodes);

        // Sector 6 Logistics includes geofencing
        $logistics = PosSector::query()->where('code', 'SECTOR_06_LOGISTICS_FLEET')->with('primitives')->firstOrFail();
        $logisticsPrimCodes = $logistics->primitives->pluck('code')->all();
        $this->assertContains('PRIMITIVE_GEOSPATIAL_GEOFENCE', $logisticsPrimCodes);

        // Sector 3 Healthcare includes biometrics
        $health = PosSector::query()->where('code', 'SECTOR_03_HEALTHCARE_CLINICAL')->with('primitives')->firstOrFail();
        $healthPrimCodes = $health->primitives->pluck('code')->all();
        $this->assertContains('PRIMITIVE_AI_VISION_BIOMETRICS', $healthPrimCodes);

        // Sector 10 Ticketing includes biometrics
        $ticketing = PosSector::query()->where('code', 'SECTOR_10_MEDIA_TICKETING')->with('primitives')->firstOrFail();
        $ticketingPrimCodes = $ticketing->primitives->pluck('code')->all();
        $this->assertContains('PRIMITIVE_AI_VISION_BIOMETRICS', $ticketingPrimCodes);
    }

    public function test_compiler_resolves_face_attendance_into_hris_with_biometrics_and_geofence(): void
    {
        $idea = 'Aplikasi presensi absensi biometrik wajah karyawan dengan deteksi liveness anti-spoofing, validasi geofence radius kantor 100m, jadwal shift roster, dan integrasi penggajian payroll PPh 21 BPJS.';

        $resolved = ProjectOsBlueprintCompilerService::resolveSectorAndPrimitives($idea);

        $this->assertNotNull($resolved['sector']);
        $this->assertSame('SECTOR_11_HRIS_INTERNAL_OPS', $resolved['sector']->code);

        $codes = $resolved['primitives']->pluck('code')->all();
        $this->assertContains('PRIMITIVE_HRIS_PAYROLL_ROSTER', $codes);
        $this->assertContains('PRIMITIVE_APPROVAL_WORKFLOW', $codes);
        $this->assertContains('PRIMITIVE_AI_VISION_BIOMETRICS', $codes);
        $this->assertContains('PRIMITIVE_GEOSPATIAL_GEOFENCE', $codes);
    }

    public function test_architecture_manifesto_dynamically_adapts_to_face_attendance_ideas(): void
    {
        $manifesto = PrdArchitectureManifestoService::evaluateArchitecture(
            'Nexacore Face Attendance OS',
            'Karyawan sering memanipulasi waktu dan menitipkan absen menggunakan foto di layar HP. Sinyal GPS sering drift di lobi kantor.',
            [
                ['title' => 'WebRTC Camera Capture & Liveness Detection', 'desc' => 'MiniFASNet anti-spoofing'],
                ['title' => 'Geofencing Radius & Anti-Mock GPS', 'desc' => 'Haversine & PostGIS check'],
                ['title' => 'Shift Roster & Payroll PPh 21 TER', 'desc' => 'Deterministic slip payroll'],
            ],
            '1. Karyawan buka PWA dan verifikasi GPS. 2. Ambil selfie 1-frame dan evaluasi liveness. 3. Vektor dicocokkan ke pgvector. 4. Status presensi dikunci.',
            ['primary_sector' => 'SECTOR_11_HRIS_INTERNAL_OPS']
        );

        $this->assertSame('AI_BIOMETRIC_SPATIAL_READY', $manifesto['ai_database_blueprint']['engine_badge']);

        $features = implode(' ', $manifesto['ai_database_blueprint']['features']);
        $this->assertStringContainsString('pgvector Biometrik Wajah 512-D', $features);
        $this->assertStringContainsString('UU PDP No. 27/2022', $features);
        $this->assertStringContainsString('PostGIS & Formula Haversine', $features);
        $this->assertStringContainsString('Anti-Fake GPS', $features);

        $toolCategories = collect($manifesto['recommended_tools'])->pluck('category')->all();
        $this->assertContains('AI Vision & Biometrics Sidecar', $toolCategories);
        $this->assertContains('Geospatial & Geofencing Engine', $toolCategories);

        $sidecar = collect($manifesto['recommended_tools'])->where('category', 'AI Vision & Biometrics Sidecar')->first();
        $this->assertStringContainsString('FastAPI + ONNX Runtime', $sidecar['name']);
        $this->assertStringContainsString('<150ms', $sidecar['desc']);
    }

    public function test_scaffold_generator_produces_full_project_os_bundle_for_biometric_face_attendance(): void
    {
        $blueprint = VisionBlueprint::query()->create([
            'client_name' => 'Ir. Hendra Pratama',
            'email' => 'hendra.nexacore@example.com',
            'phone' => '+6281234567890',
            'nama_bisnis' => 'Nexacore Solution Face Attendance',
            'masalah_utama' => 'Kecurangan absensi menggunakan foto di layar HP dan manipulasi fake GPS lokasi kantor.',
            'tujuan_utama' => 'Absensi biometrik wajah passive liveness, geofencing kantor, shift roster, dan penggajian otomatis.',
            'target_audiens' => 'Karyawan kantor, tim HRD, supervisor, dan manajemen.',
            'aktor_sistem' => "Karyawan: Clock-in selfie & pengajuan cuti\nSupervisor: Approval dinas luar kantor\nHRD: Monitoring absensi real-time & payroll",
            'fitur_wajib' => "Biometrik wajah 512-D dengan liveness check\nGeofencing radius 100m dengan anti-fake GPS\nRoster shift kerja dan kalkulasi PPh 21 TER\nAlur approval dinas luar kantor",
            'alur_kerja' => "1. Karyawan ambil selfie di PWA\n2. GPS dan keaslian wajah divalidasi\n3. Presensi dicatat ke database berstempel waktu server",
            'user_metadata' => [
                'primary_sector' => 'SECTOR_11_HRIS_INTERNAL_OPS',
            ],
        ]);
        $blueprint->generateAndSavePrd();

        $files = app(ScaffoldGeneratorService::class)->generateFiles($blueprint->fresh());

        $this->assertNotEmpty($files);
        $this->assertArrayHasKey('PROJECT_OS_EXECUTION_BLUEPRINT.md', $files);
        $this->assertArrayHasKey('.agents/BLUEPRINT_SCHEMA.json', $files);
        $this->assertArrayHasKey('docker-compose.yml', $files);

        $executionMd = $files['PROJECT_OS_EXECUTION_BLUEPRINT.md'];
        $this->assertStringContainsString('SECTOR_11_HRIS_INTERNAL_OPS', $executionMd);
    }
}
