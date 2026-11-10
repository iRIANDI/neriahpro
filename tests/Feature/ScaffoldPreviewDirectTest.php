<?php

namespace Tests\Feature;

use App\Models\VisionBlueprint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScaffoldPreviewDirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_scaffold_returns_ok_for_authorized_user(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Pastor Yoseph',
            'email' => 'pastor@church.com',
            'whatsapp' => '6281380448253',
            'nama_bisnis' => 'Hang Tuah Church Live Audio',
            'slug' => 'hang-tuah-sdac-retail-ultimate-test',
            'tipe_aplikasi' => 'Live Audio Streaming & PWA',
            'target_pengguna' => 'Jemaat & Interpreter',
            'masalah_utama' => 'Need real-time audio translation',
            'solusi_diinginkan' => 'WebRTC SFU and Reverb signaling',
            'project_status' => 'Retail License',
            'is_published' => true,
            'user_metadata' => [
                'retail_tier' => 'retail_ultimate',
                'paid' => true,
                'package_tier' => 'retail_ultimate',
            ],
            'prd_content' => [
                'features' => [
                    'mvp_phase1' => [
                        ['id' => 'FEAT-01', 'title' => 'Language Channels', 'desc' => 'Manage language channels'],
                    ],
                ],
                'erd_schema' => [
                    'tables' => [
                        [
                            'name' => 'language_channels',
                            'columns' => [
                                ['name' => 'id', 'type' => 'VARCHAR(26)', 'index' => 'PRIMARY'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $user = User::factory()->create(['email' => 'pastor@church.com']);
        $this->actingAs($user);

        $response = $this->getJson(route('blueprint.scaffold.preview', $blueprint->slug));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'slug' => $blueprint->slug,
            ]);

        $data = $response->json();
        $this->assertNotEmpty($data['files']);
        $this->assertArrayHasKey('docker-compose.yml', $data['files']);
        $this->assertArrayHasKey('README.md', $data['files']);
    }

    public function test_save_custom_folder_persists_uniquely_in_user_metadata(): void
    {
        $blueprint1 = VisionBlueprint::create([
            'client_name' => 'Pastor Yoseph',
            'email' => 'pastor@church.com',
            'whatsapp' => '6281380448253',
            'nama_bisnis' => 'Hang Tuah Church Live Audio',
            'slug' => 'hang-tuah-church-project',
            'project_status' => 'Retail License',
            'is_published' => true,
            'user_metadata' => [
                'retail_tier' => 'retail_ultimate',
                'paid' => true,
                'package_tier' => 'retail_ultimate',
            ],
        ]);

        $blueprint2 = VisionBlueprint::create([
            'client_name' => 'Pastor Yoseph',
            'email' => 'pastor@church.com',
            'whatsapp' => '6281380448253',
            'nama_bisnis' => 'PT Gaya Balical Pla',
            'slug' => 'pt-gayabalical-pla',
            'project_status' => 'Retail License',
            'is_published' => true,
            'user_metadata' => [
                'retail_tier' => 'retail_ultimate',
                'paid' => true,
                'package_tier' => 'retail_ultimate',
            ],
        ]);

        $user = User::factory()->create(['email' => 'pastor@church.com']);
        $this->actingAs($user);

        // Save custom folder for blueprint 1
        $res1 = $this->postJson(route('blueprint.scaffold.save-custom-folder', $blueprint1->slug), [
            'folder' => 'htctranslator',
        ]);

        $res1->assertOk()->assertJson([
            'success' => true,
            'custom_folder' => 'htctranslator',
            'effective_slug' => 'htctranslator',
        ]);

        $blueprint1->refresh();
        $blueprint2->refresh();

        $this->assertEquals('htctranslator', $blueprint1->user_metadata['short_slug']);
        $this->assertArrayNotHasKey('short_slug', $blueprint2->user_metadata ?? []);

        // Clearing blueprint 1 returns to empty and clears user_metadata
        $resReset = $this->postJson(route('blueprint.scaffold.save-custom-folder', $blueprint1->slug), [
            'folder' => '',
        ]);
        $resReset->assertOk()->assertJson([
            'success' => true,
            'custom_folder' => null,
        ]);

        $blueprint1->refresh();
        $this->assertArrayNotHasKey('short_slug', $blueprint1->user_metadata ?? []);
    }

    public function test_prd_markdown_exporter_safely_handles_missing_endpoint(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Test Client',
            'email' => 'test@client.com',
            'nama_bisnis' => 'Test Business',
            'slug' => 'test-business',
            'project_status' => 'Retail License',
            'is_published' => true,
        ]);

        $prd = [
            'features' => [
                'mvp_phase1' => [
                    [
                        'id' => 'FEAT-01',
                        'title' => 'Feature Without Endpoint',
                        'desc' => 'Feature testing missing endpoint resilience',
                        'integration' => [
                            // 'endpoint' is missing intentionally
                            'request_schema' => ['param' => 'val'],
                            'response_schema' => ['success' => true],
                        ],
                    ],
                ],
            ],
        ];

        $markdown = \App\Services\Prd\PrdMarkdownExporterService::toMarkdown($blueprint, $prd);
        $this->assertNotEmpty($markdown);
        $this->assertStringContainsString('API & Integration Contract', $markdown);
        $this->assertStringContainsString('/api/v1/feat-01', $markdown);
    }
}
