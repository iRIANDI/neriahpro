<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Entitlement\BlueprintEntitlementGateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BlueprintArchitectureAdvisorGatedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => '{}']]]],
                ],
                'choices' => [
                    ['message' => ['content' => '{}']],
                ],
            ], 200),
        ]);
    }
    /**
     * Test free demo sandbox is strictly blocked from scanning advisory (HTTP 403 Forbidden).
     */
    public function test_free_sandbox_blueprint_is_strictly_blocked_from_advisory_scan_with_403(): void
    {
        $freeBp = VisionBlueprint::create([
            'id' => (string) Str::ulid(),
            'slug' => 'demo-church-audio-' . strtolower((string) Str::random(6)),
            'nama_bisnis' => 'Demo Church Live Audio',
            'client_name' => 'Demo User',
            'email' => 'demouser@test.com',
            'phone' => '+628123456789',
            'masalah_utama' => 'Testing free demo restrictions',
            'fitur_wajib' => 'Audio streaming, language channel',
            'project_status' => 'Draft Proposal',
            'user_metadata' => [
                'package_tier' => 'retail_spark',
                'paid' => false,
            ],
            'prd_content' => [
                'features' => [
                    'mvp_phase1' => [
                        ['id' => 'FEAT-MVP-01', 'title' => 'Language Channel Management', 'desc' => 'Manage language channels'],
                    ],
                ],
            ],
        ]);

        $gateService = app(BlueprintEntitlementGateService::class);
        $this->assertFalse($gateService->isPurchasedPackage($freeBp));
        $this->assertFalse($freeBp->isPurchasedPackage());

        $response = $this->postJson(route('blueprint.architecture-advisory.scan', $freeBp->slug), [
            'locale' => 'id',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'code' => 'PREMIUM_LICENSE_REQUIRED',
            ]);
    }

    /**
     * Test free demo sandbox cannot integrate advisory (HTTP 403 Forbidden).
     */
    public function test_free_sandbox_blueprint_is_strictly_blocked_from_advisory_integration_with_403(): void
    {
        $freeBp = VisionBlueprint::create([
            'id' => (string) Str::ulid(),
            'slug' => 'demo-free-project-' . strtolower((string) Str::random(6)),
            'nama_bisnis' => 'Free Sandbox System',
            'client_name' => 'Free Tester',
            'email' => 'freetester@test.com',
            'phone' => '+628123456780',
            'project_status' => 'Draft Proposal',
            'user_metadata' => [
                'package_tier' => 'spark',
                'paid' => false,
            ],
            'prd_content' => [
                'features' => [
                    'mvp_phase1' => [],
                ],
            ],
        ]);

        $response = $this->postJson(route('blueprint.architecture-advisory.integrate', $freeBp->slug), [
            'selected_features' => ['ADVISORY-FEAT-QNA'],
            'selected_tech_upgrades' => ['TECH-UPGRADE-ULID-CURSOR'],
            'cached_recommendations' => [],
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'code' => 'PREMIUM_LICENSE_REQUIRED',
            ]);
    }

    /**
     * Test paid retail license can successfully scan architecture gaps and tech radar.
     */
    public function test_paid_retail_package_can_successfully_scan_architecture_gaps_and_tech_radar(): void
    {
        $paidBp = VisionBlueprint::create([
            'id' => (string) Str::ulid(),
            'slug' => 'hang-tuah-paid-audio-' . strtolower((string) Str::random(6)),
            'nama_bisnis' => 'Hang Tuah Church Live Audio',
            'client_name' => 'Pastor Yoseph',
            'email' => 'pastor@church.com',
            'phone' => '+6281380448253',
            'masalah_utama' => 'Need ultra-low latency sermon audio translation for church members with chat moderation',
            'fitur_wajib' => 'Language channels, live audio streaming',
            'project_status' => 'Retail License',
            'user_metadata' => [
                'retail_tier' => 'retail_ultimate',
                'paid' => true,
            ],
            'prd_content' => [
                'project_metadata' => ['domain_category' => 'Church Live Audio Translation'],
                'features' => [
                    'mvp_phase1' => [
                        ['id' => 'FEAT-MVP-01', 'title' => 'Language Channel Management', 'desc' => 'Manage language channels'],
                        ['id' => 'FEAT-MVP-02', 'title' => 'WebRTC Live Audio Streaming', 'desc' => 'Stream Opus audio to attendees'],
                    ],
                ],
                'erd_schema' => [
                    'tables' => [
                        [
                            'name' => 'language_channels',
                            'columns' => [
                                ['name' => 'id', 'type' => 'VARCHAR(26)', 'index' => 'PRIMARY'],
                                ['name' => 'name', 'type' => 'VARCHAR(100)', 'index' => ''],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $gateService = app(BlueprintEntitlementGateService::class);
        $this->assertTrue($gateService->isPurchasedPackage($paidBp));
        $this->assertTrue($paidBp->isPurchasedPackage());

        $user = User::factory()->create(['email' => 'pastor@church.com']);
        $this->actingAs($user);

        $response = $this->postJson(route('blueprint.architecture-advisory.scan', $paidBp->slug), [
            'locale' => 'en',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_purchased' => true,
            ])
            ->assertJsonStructure([
                'success',
                'is_purchased',
                'missing_features',
                'tech_upgrades',
            ]);

        $data = $response->json();
        $this->assertNotEmpty($data['missing_features']);
        $this->assertNotEmpty($data['tech_upgrades']);

        // Must include why_critical
        $this->assertArrayHasKey('why_critical', $data['missing_features'][0]);
    }

    /**
     * Test paid package can integrate approved features and recalculate SHA-256 seal.
     */
    public function test_paid_package_can_integrate_approved_features_and_recalculate_sha256(): void
    {
        $paidBp = VisionBlueprint::create([
            'id' => (string) Str::ulid(),
            'slug' => 'hang-tuah-integrate-test-' . strtolower((string) Str::random(6)),
            'nama_bisnis' => 'Hang Tuah Church Live Audio',
            'client_name' => 'Pastor Yoseph',
            'email' => 'pastor@church.com',
            'phone' => '+6281380448253',
            'masalah_utama' => 'Need real-time sermon translation',
            'fitur_wajib' => 'Language channels',
            'project_status' => 'Retail License',
            'user_metadata' => [
                'retail_tier' => 'retail_ultimate',
                'paid' => true,
            ],
            'prd_content' => [
                'features' => [
                    'mvp_phase1' => [
                        ['id' => 'FEAT-MVP-01', 'title' => 'Language Channel Management', 'desc' => 'Manage language channels'],
                    ],
                    'roadmap_phase2' => [],
                ],
                'erd_schema' => [
                    'tables' => [
                        ['name' => 'language_channels', 'columns' => [['name' => 'id', 'type' => 'VARCHAR(26)', 'index' => 'PRIMARY']]],
                    ],
                ],
            ],
        ]);

        $initialHash = $paidBp->calculatePrdHash();

        $user = User::factory()->create(['email' => 'pastor@church.com']);
        $this->actingAs($user);

        $response = $this->postJson(route('blueprint.architecture-advisory.integrate', $paidBp->slug), [
            'selected_features' => ['ADVISORY-FEAT-QNA'],
            'selected_tech_upgrades' => ['TECH-UPGRADE-ULID-CURSOR', 'TECH-UPGRADE-REVERB'],
            'cached_recommendations' => [
                [
                    'id' => 'ADVISORY-FEAT-QNA',
                    'title' => 'Attendee Real-Time Q&A & Prayer Request Channel',
                    'desc' => 'Dedicated text channel allowing attendees to submit sermon questions or prayer requests',
                    'why_critical' => 'Enables congregation engagement without disrupting main sermon audio',
                    'proposed_phase' => 'mvp',
                ],
            ],
            'locale' => 'id',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'added_count' => 1,
            ]);

        $paidBp->refresh();

        // Must have updated feature count
        $mvpCount = count($paidBp->prd_content['features']['mvp_phase1'] ?? []);
        $this->assertEquals(2, $mvpCount);

        // Feature must contain complete 6 dimensions (acceptance_criteria, backend target_files, etc.)
        $integratedFeat = $paidBp->prd_content['features']['mvp_phase1'][1];
        $this->assertEquals('Attendee Real-Time Q&A & Prayer Request Channel', $integratedFeat['title']);
        $this->assertNotEmpty($integratedFeat['acceptance_criteria']);
        $this->assertNotEmpty($integratedFeat['backend']['target_files']);

        // SHA-256 seal must be updated and match current PRD
        $newHash = $paidBp->calculatePrdHash();
        $this->assertNotEquals($initialHash, $newHash);
        $this->assertEquals($newHash, $paidBp->document_sha256);
    }
}
