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

    public function test_ai_agent_module_51_native_tools_and_execution_engine_work_for_biometrics_and_spatial(): void
    {
        $this->seed(\Database\Seeders\SuperAdminSeeder::class);

        // 1. Verify 51 tools registered in Gemini & OpenAI declarations
        $declarations = \App\Services\Ai\GeminiToolRegistry::getDeclarations();
        $this->assertCount(51, $declarations);
        $toolNames = collect($declarations)->pluck('name')->all();
        $this->assertContains('verify_biometric_face_vector', $toolNames);
        $this->assertContains('evaluate_geofence_and_spatial_presence', $toolNames);
        $this->assertContains('purge_biometric_vector_right_to_erasure', $toolNames);

        $engine = app(\App\Services\Ai\AgenticActionExecutionEngine::class);

        // 2. Test verify_biometric_face_vector
        $bioSuccess = $engine->execute('verify_biometric_face_vector', [
            'user_id' => '01J9ATTENDANCEUSER00000001',
            'liveness_score' => 0.94,
            'cosine_similarity' => 0.82,
            'device_fingerprint' => 'FINGERPRINT_WEB_CAMERA_OK',
        ]);
        $this->assertTrue($bioSuccess['success']);
        $this->assertSame('BIOMETRIC_VERIFIED_SUCCESS', $bioSuccess['verdict']);

        $bioSpoof = $engine->execute('verify_biometric_face_vector', [
            'user_id' => '01J9ATTENDANCEUSER00000001',
            'liveness_score' => 0.40, // Below 0.85 threshold
            'cosine_similarity' => 0.90,
        ]);
        $this->assertFalse($bioSpoof['success']);
        $this->assertSame('SPOOF_ATTACK_DETECTED_LIVENESS_FAILED', $bioSpoof['verdict']);

        // 3. Test evaluate_geofence_and_spatial_presence
        $geoValid = $engine->execute('evaluate_geofence_and_spatial_presence', [
            'user_id' => '01J9ATTENDANCEUSER00000001',
            'user_latitude' => -8.670458,
            'user_longitude' => 115.212629,
            'office_latitude' => -8.670460,
            'office_longitude' => 115.212630,
            'radius_meters' => 100,
            'device_timestamp' => time(),
            'accuracy_meters' => 12.0,
            'is_mock_provider' => false,
        ]);
        $this->assertTrue($geoValid['success']);
        $this->assertSame('IN_GEOFENCE_VALID', $geoValid['status']);
        $this->assertTrue($geoValid['is_within_geofence']);

        // Mock GPS blocked
        $geoMock = $engine->execute('evaluate_geofence_and_spatial_presence', [
            'user_id' => '01J9ATTENDANCEUSER00000001',
            'user_latitude' => -8.670458,
            'user_longitude' => 115.212629,
            'office_latitude' => -8.670460,
            'office_longitude' => 115.212630,
            'radius_meters' => 100,
            'device_timestamp' => time() - 120, // 2 minutes drift
            'is_mock_provider' => true,
        ]);
        $this->assertFalse($geoMock['success']);
        $this->assertSame('MOCK_GPS_FLAGGED_BLOCKED', $geoMock['status']);

        // Out of office pending approval
        $geoOutOfOffice = $engine->execute('evaluate_geofence_and_spatial_presence', [
            'user_id' => '01J9ATTENDANCEUSER00000001',
            'user_latitude' => -8.500000,
            'user_longitude' => 115.100000, // Outside office
            'office_latitude' => -8.670460,
            'office_longitude' => 115.212630,
            'radius_meters' => 100,
            'device_timestamp' => time(),
            'accuracy_meters' => 10.0,
            'out_of_office_reason' => 'Meeting koordinasi lapangan dengan klien di Gianyar',
        ]);
        $this->assertTrue($geoOutOfOffice['success']);
        $this->assertSame('OUT_OF_GEOFENCE_PENDING_APPROVAL', $geoOutOfOffice['status']);
        $this->assertNotNull($geoOutOfOffice['out_of_office_routing']);

        // 4. Test purge_biometric_vector_right_to_erasure (as super admin)
        $admin = \App\Models\User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($admin);

        $purgeResult = $engine->execute('purge_biometric_vector_right_to_erasure', [
            'user_id' => '01J9ATTENDANCEUSER00000001',
            'reason' => 'Karyawan resign dan mengajukan hak dilupakan UU PDP',
            'confirmation_token' => 'PURGE_BIOMETRICS_CONFIRMED',
        ]);
        $this->assertTrue($purgeResult['success']);
        $this->assertSame('REVOKED_FORGOTTEN', $purgeResult['status']);
        $this->assertNotEmpty($purgeResult['forensic_audit_hash']);
    }

    public function test_rbac_and_audit_trail_for_biometrics_and_geofencing_are_strictly_isolated(): void
    {
        $this->seed(\Database\Seeders\SuperAdminSeeder::class);

        // Permissions exist in DB
        foreach ([
            'manage_biometrics_and_liveness',
            'purge_biometrics_right_to_erasure',
            'manage_geofence_and_spatial_zones',
            'approve_out_of_office_presence',
        ] as $perm) {
            $this->assertTrue(\Spatie\Permission\Models\Permission::query()->where('name', $perm)->exists());
        }

        // Super Admin has them
        $admin = \App\Models\User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->assertTrue($admin->can('manage_biometrics_and_liveness'));
        $this->assertTrue($admin->can('purge_biometrics_right_to_erasure'));

        // Midtrans reviewer strictly forbidden
        $reviewer = \App\Models\User::query()->where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();
        $this->assertFalse($reviewer->can('manage_biometrics_and_liveness'));
        $this->assertFalse($reviewer->can('purge_biometrics_right_to_erasure'));
        $this->assertFalse($reviewer->can('manage_geofence_and_spatial_zones'));
    }

    public function test_rag_knowledge_base_syncs_composable_primitives_into_pgvector(): void
    {
        $sync = app(\App\Services\Ai\VectorEmbeddingService::class)->syncSystemKnowledgeBase(true);
        $this->assertSame('success', $sync['status']);
        $this->assertGreaterThanOrEqual(18, $sync['synced_sections']);

        $vector = \App\Models\AiKnowledgeVector::query()
            ->where('source_id', 'composable-primitives-biometrics-and-geofence-v14')
            ->firstOrFail();

        $this->assertStringContainsString('PRIMITIVE_AI_VISION_BIOMETRICS', $vector->content);
        $this->assertStringContainsString('PRIMITIVE_GEOSPATIAL_GEOFENCE', $vector->content);
        $this->assertStringContainsString('ArcFace', $vector->content);
        $this->assertStringContainsString('MiniFASNet', $vector->content);
        $this->assertStringContainsString('Haversine', $vector->content);
    }
}

