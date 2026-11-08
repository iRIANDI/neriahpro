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
}
