<?php

namespace Tests\Feature;

use App\Models\VisionBlueprint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RetailBlueprintEditAndSessionLeakTest extends TestCase
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
     * Test that opening /blueprint?slug={slug} for a retail license retains Retail Track and does not leak Studio 50/50 terms.
     */
    public function test_retail_blueprint_edit_retains_retail_track_and_prevents_studio_leakage(): void
    {
        $blueprint = VisionBlueprint::create([
            'nama_bisnis' => 'HTC Church Translation App',
            'client_name' => 'Yoseph Iriandi',
            'email' => 'yoseph@example.com',
            'phone' => '+6281380448253',
            'masalah_utama' => 'Need church real-time multilingual translator application.',
            'tujuan_utama' => 'Provide real-time translation for international congregation.',
            'target_audiens' => 'Church congregation and foreign visitors',
            'aktor_sistem' => 'Admin, Translator, Listener',
            'fitur_wajib' => 'Language channels, live audio streaming, moderation chat',
            'alur_kerja' => 'Translator speaks -> Audio streamed to listener channel',
            'project_status' => 'Retail License',
            'signed_agreement' => true,
            'is_published' => true,
            'user_metadata' => [
                'retail_tier' => 'retail_pro',
                'package_tier' => 'retail_pro',
            ],
        ]);

        $response = $this->get('/blueprint?slug=' . $blueprint->slug);

        $response->assertStatus(200);
        $initialData = $response->viewData('initialData');

        $this->assertNotNull($initialData);
        $this->assertEquals('retail', $initialData['selected_track']);
        $this->assertEquals('retail', $initialData['track']);
        $this->assertEquals($blueprint->slug, $initialData['_meta']['is_editing_slug']);
        
        // Assert budget and payment terms do NOT contain Studio terms
        $this->assertStringNotContainsString('15.000.000', $initialData['kisaranBudget']);
        $this->assertStringNotContainsString('Growth Monolith', $initialData['kisaranBudget']);
        $this->assertStringNotContainsString('50/50', $initialData['terminPembayaran']);
        $this->assertStringNotContainsString('DP Kickoff', $initialData['terminPembayaran']);
    }

    /**
     * Test that submitting an edit via /api/vision-blueprint updates the existing record in-place instead of creating a duplicate.
     */
    public function test_submitting_edit_updates_existing_retail_blueprint_in_place(): void
    {
        $blueprint = VisionBlueprint::create([
            'nama_bisnis' => 'Translator Apps HTC',
            'client_name' => 'Yoseph Iriandi',
            'email' => 'yoseph@example.com',
            'phone' => '+6281380448253',
            'masalah_utama' => 'Original Problem Statement',
            'tujuan_utama' => 'Original Goal',
            'target_audiens' => 'Congregation',
            'aktor_sistem' => 'Translator, Listener',
            'fitur_wajib' => 'Channels, Stream',
            'alur_kerja' => 'Workflow original',
            'project_status' => 'Retail License',
            'signed_agreement' => true,
            'is_published' => true,
            'user_metadata' => [
                'retail_tier' => 'retail_pro',
            ],
        ]);

        $initialCount = VisionBlueprint::count();

        $payload = [
            'slug' => $blueprint->slug,
            'is_editing_slug' => $blueprint->slug,
            'namaBisnis' => 'Translator Apps HTC Pro Edition',
            'clientName' => 'Yoseph Iriandi',
            'email' => 'yoseph@example.com',
            'phone' => '+6281380448253',
            'masalahUtama' => 'Refined Multilingual live audio translation for international church service.',
            'tujuanUtama' => 'Zero-latency audio streaming for Sunday services.',
            'targetAudiens' => 'International church guests and translators',
            'aktorSistem' => 'Superadmin, Multimedia Operator, Translator, Listener',
            'fiturWajib' => '1. Multilingual Audio Streaming 2. Language Channel Management 3. Chat Moderation',
            'alurKerja' => 'Translator speaks -> Audio encoded -> Listener selects channel and listens in real time.',
            'track' => 'retail',
            'selected_track' => 'retail',
        ];

        $response = $this->postJson('/api/vision-blueprint', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'slug' => $blueprint->slug,
            ],
        ]);

        // Assert no new record was created
        $this->assertEquals($initialCount, VisionBlueprint::count());

        // Assert the existing record was updated
        $blueprint->refresh();
        $this->assertEquals('Translator Apps HTC Pro Edition', $blueprint->nama_bisnis);
        $this->assertEquals('Retail License', $blueprint->project_status);
        $this->assertEquals('Refined Multilingual live audio translation for international church service.', $blueprint->masalah_utama);
    }

    /**
     * Test that autosave on a paid retail license does not fail with SCOPE_LOCKED 403.
     */
    public function test_autosave_on_paid_retail_license_is_permitted(): void
    {
        $blueprint = VisionBlueprint::create([
            'nama_bisnis' => 'Translator Apps HTC',
            'client_name' => 'Yoseph Iriandi',
            'email' => 'yoseph@example.com',
            'phone' => '+6281380448253',
            'masalah_utama' => 'Problem Statement',
            'tujuan_utama' => 'Goal',
            'target_audiens' => 'Congregation',
            'aktor_sistem' => 'Translator',
            'fitur_wajib' => 'Audio',
            'alur_kerja' => 'Workflow',
            'project_status' => 'Retail License',
            'signed_agreement' => true, // Would cause isScopeFrozen() to return true
            'is_published' => true,
            'user_metadata' => [
                'retail_tier' => 'retail_pro',
            ],
        ]);

        $response = $this->postJson('/api/blueprint/autosave', [
            'is_explicit_slug_edit' => true,
            'slug' => $blueprint->slug,
            'draft_id' => 'draft_test_123',
            'blueprint' => [
                'namaBisnis' => 'Translator Apps HTC Updated',
                'targetPlatform' => 'Modern Web Application Responsive & PWA',
                '_meta' => [
                    'is_editing_slug' => $blueprint->slug,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test that supplementIdea on a paid retail license is permitted and not blocked by studio scope freeze.
     */
    public function test_supplement_idea_on_paid_retail_license_is_permitted(): void
    {
        $blueprint = VisionBlueprint::create([
            'nama_bisnis' => 'Translator Apps HTC',
            'client_name' => 'Yoseph Iriandi',
            'email' => 'yoseph@example.com',
            'phone' => '+6281380448253',
            'masalah_utama' => 'Problem Statement',
            'tujuan_utama' => 'Goal',
            'target_audiens' => 'Congregation',
            'aktor_sistem' => 'Translator',
            'fitur_wajib' => 'Audio',
            'alur_kerja' => 'Workflow',
            'project_status' => 'Retail License',
            'signed_agreement' => true, // Would cause isScopeFrozen() to return true
            'is_published' => true,
            'user_metadata' => [
                'retail_tier' => 'retail_pro',
            ],
        ]);

        $response = $this->postJson('/api/blueprint/supplement-idea', [
            'supplement_text' => 'Tambahkan fitur auto-reconnect WebSocket saat koneksi internet terputus di gereja.',
            'blueprint' => [
                'namaBisnis' => $blueprint->nama_bisnis,
                'masalahUtama' => $blueprint->masalah_utama,
                'tujuanUtama' => $blueprint->tujuan_utama,
                'targetAudiens' => $blueprint->target_audiens,
                'aktorSistem' => $blueprint->aktor_sistem,
                'fiturWajib' => $blueprint->fitur_wajib,
                'alurKerja' => $blueprint->alur_kerja,
                '_meta' => [
                    'is_editing_slug' => $blueprint->slug,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test that an authenticated user who owns a retail license automatically defaults to Retail track with verified customer flag.
     */
    public function test_authenticated_user_with_paid_retail_license_defaults_to_retail_track_and_flags_customer(): void
    {
        $user = User::factory()->create([
            'email' => 'yoseph.retail@example.com',
        ]);

        VisionBlueprint::create([
            'user_id' => $user->id,
            'nama_bisnis' => 'My First Retail Blueprint',
            'client_name' => 'Yoseph Iriandi',
            'email' => $user->email,
            'phone' => '+6281380448253',
            'project_status' => 'Retail License',
            'signed_agreement' => true,
            'is_published' => true,
            'user_metadata' => [
                'retail_tier' => 'retail_pro',
            ],
        ]);

        $response = $this->actingAs($user)->get('/blueprint?tier=spark');

        $response->assertStatus(200);
        $initialData = $response->viewData('initialData');

        $this->assertNotNull($initialData);
        $this->assertEquals('retail', $initialData['selected_track']);
        $this->assertEquals('retail', $initialData['track']);
        $this->assertTrue($initialData['is_retail_customer']);
        $this->assertTrue($initialData['has_paid_retail_license']);
    }

    /**
     * Test that PRD Architecture Manifesto generates comprehensive Cross-Platform Desktop, Mobile, and WebRTC Audio architecture.
     */
    public function test_prd_architecture_manifesto_supports_cross_platform_desktop_mobile_and_realtime_audio(): void
    {
        $manifesto = \App\Services\Prd\PrdArchitectureManifestoService::generateMobileAndSyncArchitecture(
            'Translator Apps HTC',
            'Cross-Platform Desktop (Windows, macOS, Linux via Tauri / Flutter Desktop) + Real-Time WebRTC / WebSockets',
            [['name' => 'language_channels']],
            [['title' => 'WebRTC Audio Stream']]
        );

        $this->assertArrayHasKey('mobile_platform', $manifesto);
        $this->assertArrayHasKey('desktop_platform', $manifesto);
        $this->assertArrayHasKey('realtime_audio_webrtc', $manifesto);

        // Verify desktop platforms (Windows, macOS, Linux via Tauri / Flutter)
        $this->assertStringContainsString('Windows 10/11', $manifesto['desktop_platform']['target_os']);
        $this->assertStringContainsString('macOS', $manifesto['desktop_platform']['target_os']);
        $this->assertStringContainsString('Linux', $manifesto['desktop_platform']['target_os']);
        $this->assertStringContainsString('Tauri 2.x', $manifesto['desktop_platform']['framework']);

        // Verify low-latency real-time WebRTC audio architecture
        $this->assertStringContainsString('LiveKit', $manifesto['realtime_audio_webrtc']['engine']);
        $this->assertStringContainsString('150ms', $manifesto['realtime_audio_webrtc']['latency']);
        $this->assertStringContainsString('Opus 48kHz', $manifesto['realtime_audio_webrtc']['codec']);
    }
}
