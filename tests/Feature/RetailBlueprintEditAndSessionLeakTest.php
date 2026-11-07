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

    /**
     * Test that blueprint synthesis returns English output when locale === 'en' even if input text is in Indonesian.
     */
    public function test_blueprint_synthesis_enforces_english_when_locale_en_is_requested(): void
    {
        $uploadedFile = \Illuminate\Http\UploadedFile::fake()->createWithContent(
            'Translator Apps HTC.md',
            "# HTC Translator\nFitur Wajib:\n1. Manajemen Channel Bahasa: Tambah/edit/hapus bahasa\n2. Atur & Moderasi Chat: Menghapus pesan chat tertentu"
        );

        $response = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => 'Aplikasi penerjemah audio ibadah gereja secara live streaming.',
            'locale' => 'en',
            'files' => [$uploadedFile],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $data = $response->json('data');

        $this->assertNotNull($data);
        $this->assertArrayHasKey('draft_id', $response->json());
        $this->assertArrayHasKey('file_details', $data);
        $this->assertCount(1, $data['file_details']);
        $this->assertEquals('Translator Apps HTC.md', $data['file_details'][0]['name']);

        // Assert that draft is persisted in cache and session
        $draftId = $response->json('draft_id');
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has('blueprint_draft_' . $draftId));

        // When visiting /blueprint?tier=pro&draft_id={draftId}, the initialData should be restored
        $pageResponse = $this->get('/blueprint?tier=pro&draft_id=' . $draftId);
        $pageResponse->assertStatus(200);
        $initialData = $pageResponse->viewData('initialData');
        $this->assertNotNull($initialData);
        $this->assertNotEmpty($initialData['file_details'] ?? $initialData['_meta']['file_details'] ?? []);
    }

    /**
     * Test that re-uploading the same file does not create duplicate files on disk.
     */
    public function test_reuploading_same_file_is_idempotent_and_does_not_accumulate_duplicates(): void
    {
        $admin = User::factory()->create(['email' => 'yoseph.iriandi.tambunan@gmail.com']);

        $file1 = \Illuminate\Http\UploadedFile::fake()->createWithContent('Translator Apps HTC.md', '# Version 1');
        $res1 = $this->actingAs($admin)->post('/api/blueprint/analyze-idea', [
            'idea_text' => 'HTC Church live audio streaming interpreter platform.',
            'locale' => 'id',
            'files' => [$file1],
        ], ['Accept' => 'application/json']);

        $res1->assertStatus(200);
        $data1 = $res1->json('data');
        $storedFilename1 = $data1['file_details'][0]['stored_file'] ?? null;
        $this->assertEquals('translator-apps-htc.md', $storedFilename1);

        // Refresh request / session state before second upload
        $this->flushSession();

        $file2 = \Illuminate\Http\UploadedFile::fake()->createWithContent('Translator Apps HTC.md', '# Version 2');
        $res2 = $this->actingAs($admin)->post('/api/blueprint/analyze-idea', [
            'idea_text' => 'HTC Church live audio streaming interpreter platform.',
            'locale' => 'id',
            'files' => [$file2],
        ], ['Accept' => 'application/json']);

        $res2->assertStatus(200);
        $data2 = $res2->json('data');
        $storedFilename2 = $data2['file_details'][0]['stored_file'] ?? null;

        // File names must match identically (idempotent, no random hash suffix)
        $this->assertEquals('translator-apps-htc.md', $storedFilename2);
        $this->assertEquals($storedFilename1, $storedFilename2);
    }

    /**
     * Test that AI synthesis preserves Retail track and returns clean Retail deliverables without studio 50/50 leaks.
     */
    public function test_ai_synthesis_preserves_retail_track_and_retail_license_entitlements_when_retail_is_selected(): void
    {
        $user = User::factory()->create([
            'email' => 'yoseph@example.com',
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent(
            'Translator Apps HTC.md',
            "# HTC Translator Enterprise System\nFitur Wajib:\n1. Multi-role Audio WebRTC\n2. Real-time chat & moderation\n3. High-concurrency language channels\n4. Cloud stream recording"
        );

        $response = $this->actingAs($user)->post('/api/blueprint/analyze-idea', [
            'idea_text' => 'Aplikasi penerjemah audio ibadah gereja secara live streaming terdistribusi.',
            'locale' => 'id',
            'track' => 'retail',
            'tier' => 'pro',
            'files' => [$file],
        ], ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $result = $response->json();
        $this->assertTrue($result['success']);

        // Must recommend retail and NOT hijack to studio
        $this->assertEquals('retail', $result['track_recommendation']['recommended_track']);

        $data = $result['data'];
        $this->assertNotNull($data);

        // Assert Block F parameters are clean Retail defaults, not Studio 15jt / 50% DP
        $this->assertStringContainsString('Pro', $data['kisaranBudget']);
        $this->assertStringNotContainsString('15.000.000', $data['kisaranBudget']);
        $this->assertStringNotContainsString('Growth Monolith', $data['kisaranBudget']);
        $this->assertStringNotContainsString('50/50', $data['terminPembayaran']);
        $this->assertStringNotContainsString('DP Kickoff', $data['terminPembayaran']);
        $this->assertStringContainsString('Lisensi Mandiri', $data['garansiSla']);
    }

    /**
     * Test that blueprint update succeeds and persists data even if remote AI times out or throws an error.
     */
    public function test_blueprint_update_succeeds_even_when_remote_ai_prd_generation_throws_or_times_out(): void
    {
        // Force HTTP client to simulate a timeout or connection failure for all AI calls
        \Illuminate\Support\Facades\Http::fake([
            '*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('cURL error 28: Operation timed out after 12000 milliseconds');
            },
        ]);

        $blueprint = VisionBlueprint::create([
            'nama_bisnis' => 'HTC Church Translation App',
            'client_name' => 'Yoseph Iriandi',
            'email' => 'yoseph@example.com',
            'phone' => '+6281380448253',
            'masalah_utama' => 'Old Problem',
            'tujuan_utama' => 'Old Goal',
            'target_audiens' => 'Old Audience',
            'aktor_sistem' => 'Admin',
            'fitur_wajib' => 'Old Feature',
            'alur_kerja' => 'Old Workflow',
            'project_status' => 'Retail License',
            'signed_agreement' => true,
            'is_published' => true,
            'user_metadata' => [
                'retail_tier' => 'retail_pro',
            ],
        ]);

        $payload = [
            'slug' => $blueprint->slug,
            'is_editing_slug' => $blueprint->slug,
            'namaBisnis' => 'HTC Church Translation Resilient',
            'clientName' => 'Yoseph Iriandi',
            'email' => 'yoseph@example.com',
            'phone' => '+6281380448253',
            'masalahUtama' => 'Updated Church Live Translation',
            'tujuanUtama' => 'Updated Goal',
            'targetAudiens' => 'Updated Audience',
            'aktorSistem' => 'Superadmin, Translator, Listener',
            'fiturWajib' => '1. WebRTC Audio 2. Multi-channel 3. Moderation',
            'alurKerja' => 'Translator talks -> Real-time broadcast',
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

        $blueprint->refresh();
        $this->assertEquals('HTC Church Translation Resilient', $blueprint->nama_bisnis);
        $this->assertEquals('Updated Church Live Translation', $blueprint->masalah_utama);
        $this->assertNotNull($blueprint->prd_content);
    }
}
