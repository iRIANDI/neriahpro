<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Pages\CvProFinanceReportPage;
use App\Models\CvFinanceRecord;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Incident\ClientIncidentReportService;
use App\Services\Prd\PrdArchitectureManifestoService;
use App\Services\ScaffoldGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Models\Role;
use Tests\TestCase;

class CvProFinanceAndBlackBoxTelemetryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'email' => 'admin@neriahpro.com',
        ]);
        $this->admin->assignRole('super_admin');
    }

    public function test_cv_pro_finance_report_page_can_be_rendered(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CvProFinanceReportPage::class)
            ->assertSuccessful()
            ->assertSee('Laporan Keuangan CV Pro Enterprise Studio')
            ->assertSee('Buku Kas Transaksi Layanan Pembuatan CV')
            ->assertSee('Total Omset Layanan');
    }

    public function test_cv_pro_finance_kpi_calculations(): void
    {
        $this->actingAs($this->admin);

        // Verify initial empty state
        $component = Livewire::test(CvProFinanceReportPage::class);
        $emptyKpis = $component->get('kpis');
        $this->assertEquals(0, $emptyKpis['total_omset']);
        $this->assertEquals(0, $emptyKpis['total_count']);

        // Create explicit test records (3 paid = Rp 525.000, 1 pending)
        CvFinanceRecord::create([
            'trx_code' => 'TEST-001',
            'transaction_date' => '2026-10-01',
            'client_name' => 'Klien Test A',
            'client_email' => 'a@test.com',
            'package_name' => 'Standard',
            'amount_idr' => 75000,
            'payment_method' => 'QRIS',
            'status' => 'paid',
        ]);
        CvFinanceRecord::create([
            'trx_code' => 'TEST-002',
            'transaction_date' => '2026-10-02',
            'client_name' => 'Klien Test B',
            'client_email' => 'b@test.com',
            'package_name' => 'Full Stack',
            'amount_idr' => 150000,
            'payment_method' => 'BCA',
            'status' => 'paid',
        ]);
        CvFinanceRecord::create([
            'trx_code' => 'TEST-003',
            'transaction_date' => '2026-10-03',
            'client_name' => 'Klien Test C',
            'client_email' => 'c@test.com',
            'package_name' => 'VIP',
            'amount_idr' => 300000,
            'payment_method' => 'Mandiri',
            'status' => 'paid',
        ]);
        CvFinanceRecord::create([
            'trx_code' => 'TEST-004',
            'transaction_date' => '2026-10-04',
            'client_name' => 'Klien Test D',
            'client_email' => 'd@test.com',
            'package_name' => 'Standard',
            'amount_idr' => 75000,
            'payment_method' => 'QRIS',
            'status' => 'pending',
        ]);

        $component = Livewire::test(CvProFinanceReportPage::class);

        $kpis = $component->get('kpis');
        $this->assertIsArray($kpis);
        $this->assertArrayHasKey('total_omset', $kpis);
        $this->assertArrayHasKey('aov', $kpis);
        $this->assertArrayHasKey('total_count', $kpis);
        $this->assertArrayHasKey('success_rate', $kpis);

        $this->assertEquals(525000, $kpis['total_omset']);
        $this->assertEquals(175000, $kpis['aov']);
        $this->assertEquals(4, $kpis['total_count']);
        $this->assertEquals(75, $kpis['success_rate']);
    }

    public function test_admin_can_record_new_cv_transaction(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CvProFinanceReportPage::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->set('newClientName', 'Kurniawan Pratama, S.T.')
            ->set('newClientEmail', 'kurniawan@perusahaan.com')
            ->set('newPackageName', 'Standard (ATS Optimized)')
            ->set('newAmountIdr', 75000)
            ->set('newPaymentMethod', 'Midtrans QRIS')
            ->set('newStatus', 'paid')
            ->call('saveTransaction')
            ->assertSet('showCreateModal', false)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('cv_finance_records', [
            'client_name' => 'Kurniawan Pratama, S.T.',
            'amount_idr' => 75000,
            'status' => 'paid',
        ]);
    }

    public function test_admin_can_export_finance_csv(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(CvProFinanceReportPage::class);
        $response = $component->instance()->exportCsv();

        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('buku-kas-cv-pro-', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_black_box_telemetry_endpoint_ingests_errors_with_strict_pii_scrubbing(): void
    {
        $payload = [
            'error_message' => 'Uncaught TypeError: Cannot read property checkout of undefined at password=SecretPassword123',
            'error_type' => 'WINDOW_ON_ERROR',
            'file_url' => 'https://neriahpro.com/checkout?token=ey1234567890abcdef.1234567890abcdef.1234567890abcdef',
            'line_number' => 142,
            'stack_trace' => 'Error: payment failed with card 4111 2222 3333 4444 Bearer my_secret_token_123',
            'device_info' => [
                'device' => 'iPhone',
                'os' => 'iOS 14.4',
                'browser' => 'Safari',
            ],
            'current_url' => 'https://neriahpro.com/checkout?card=4111222233334444',
            // Sensitive fields injected by accident
            'password' => 'supersecret',
            'token' => 'jwt_secret',
            'cvv' => '123',
        ];

        $response = $this->postJson('/api/telemetry/client-error', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'recorded',
                    'pii_scrubbed' => true,
                ],
            ]);
    }

    public function test_pii_scrubber_redacts_passwords_tokens_and_credit_cards(): void
    {
        $dirtyText = 'User input with card 4111 2222 3333 4444 and Bearer token_secret_12345 and password=TopSecretPass';
        $cleaned = ClientIncidentReportService::scrubPiiString($dirtyText);

        $this->assertStringNotContainsString('4111 2222 3333 4444', $cleaned);
        $this->assertStringContainsString('[REDACTED_CREDIT_CARD]', $cleaned);

        $this->assertStringNotContainsString('token_secret_12345', $cleaned);
        $this->assertStringContainsString('Bearer [REDACTED_TOKEN]', $cleaned);

        $this->assertStringNotContainsString('TopSecretPass', $cleaned);
        $this->assertStringContainsString('password=[REDACTED]', $cleaned);

        // Test array recursive scrubbing
        $dirtyArray = [
            'username' => 'budi',
            'password' => 'myPassword',
            'credit_card' => '1234567812345678',
            'meta' => [
                'token' => 'secret_jwt',
                'description' => 'Normal text',
            ],
        ];
        $cleanArray = ClientIncidentReportService::scrubPii($dirtyArray);

        $this->assertEquals('[REDACTED_SENSITIVE_DATA]', $cleanArray['password']);
        $this->assertEquals('[REDACTED_SENSITIVE_DATA]', $cleanArray['credit_card']);
        $this->assertEquals('[REDACTED_SENSITIVE_DATA]', $cleanArray['meta']['token']);
        $this->assertEquals('Normal text', $cleanArray['meta']['description']);
    }

    public function test_scaffold_generator_includes_black_box_telemetry_files(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Apex Admin',
            'email' => 'apex@logistics.com',
            'whatsapp' => '628123456789',
            'nama_bisnis' => 'Apex Logistics Platform',
            'slug' => 'apex-logistics',
        ]);

        $files = ScaffoldGeneratorService::generateFiles($blueprint);

        $this->assertArrayHasKey('resources/js/telemetry/blackbox-error-catcher.js', $files);
        $this->assertArrayHasKey('app/Http/Controllers/Telemetry/ClientErrorTelemetryController.php', $files);

        $telemetryJs = $files['resources/js/telemetry/blackbox-error-catcher.js'];
        $this->assertStringContainsString('The Wahyu Ivan Standard', $telemetryJs);
        $this->assertStringContainsString('scrubPii', $telemetryJs);
        $this->assertStringContainsString('/api/telemetry/client-error', $telemetryJs);

        $controllerPhp = $files['app/Http/Controllers/Telemetry/ClientErrorTelemetryController.php'];
        $this->assertStringContainsString('ClientErrorTelemetryController', $controllerPhp);
        $this->assertStringContainsString('scrubPii', $controllerPhp);
    }

    public function test_prd_architecture_manifesto_includes_black_box_monitoring_pillar(): void
    {
        $advisory = PrdArchitectureManifestoService::generateWorldClassStandardAdvisory('Test Project', false);

        $this->assertArrayHasKey('strategic_pillars', $advisory);
        $pillars = $advisory['strategic_pillars'];

        $foundWahyuIvanPillar = false;
        foreach ($pillars as $p) {
            if (str_contains($p['area'], 'Black Box Error Monitoring') || str_contains($p['area'], 'Wahyu Ivan')) {
                $foundWahyuIvanPillar = true;
                $this->assertStringContainsString('iPhone lama', $p['world_class_option']);
                $this->assertStringContainsString('Log Scrubber Anti-Leak', $p['world_class_option']);
                break;
            }
        }

        $this->assertTrue($foundWahyuIvanPillar, 'Pillar Black Box Error Monitoring (Wahyu Ivan Standard) must be present in PRD World Class Advisory');
    }
}
