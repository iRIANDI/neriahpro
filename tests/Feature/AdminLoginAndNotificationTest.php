<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminLoginAndNotificationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test that the Filament login page renders successfully with Neriah Pro vision, mission & branding.
     */
    public function test_filament_admin_login_page_renders_with_neriah_pro_mission(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Neriah');
        $response->assertSee('Control Hub');
        $response->assertSee('Scope Lock OS');
        $response->assertSee('Vision Blueprint');
        $response->assertSee('CV Pro Studio');
        $response->assertDontSee('yoseph.iriandi.tambunan@gmail.com');
        $response->assertDontSee('PostgreSQL 16');
        $response->assertSee('reviewer.midtrans@neriahpro.com');
    }

    /**
     * Test that notifications JSON data column query executes cleanly without syntax or operator errors.
     */
    public function test_notifications_data_json_query_executes_cleanly(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test Admin',
                'email' => 'test-admin-' . Str::random(5) . '@neriahpro.com',
                'password' => bcrypt('secret123'),
            ]);
        }

        // Test the exact Filament notifications query that previously failed on PostgreSQL
        $count = $user->notifications()->where('data->format', 'filament')->unread()->count();

        $this->assertIsInt($count);
    }

    /**
     * Test that the Filament dashboard renders updated Project OS pillars and Frontpage quick launch widgets.
     */
    public function test_filament_dashboard_renders_updated_project_os_and_frontpage_quick_launch(): void
    {
        $admin = User::create([
            'name' => 'Yoseph Iriandi',
            'email' => 'yoseph.iriandi.tambunan@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);

        // Verify updated Project OS features
        $response->assertSee('NERIAH PRO // PROJECT OS');
        $response->assertSee('DIGITAL ARCHITECTURE PLATFORM');
        $response->assertSee('26 ARCHITECTURAL PARAMETERS');
        $response->assertSee('Kuesioner 26 Parameter Inti (Blok A-F)');
        $response->assertSee('Quick Idea Studio');
        $response->assertSee('Multi-Doc Ingestion');
        $response->assertSee('Ultimate PRD, ERD');
        $response->assertSee('Boilerplate .zip');
        $response->assertSee('AI-Shield');
        $response->assertSee('Secure Ingestion Pipeline');

        // Verify Frontpage quick launch widget
        $response->assertSee('PORTAL PUBLIK');
        $response->assertSee('Buka Beranda Utama Website (neriahpro.com)');
        $response->assertSee('/blueprint');
        $response->assertSee('/cv-pro');
        $response->assertSee('/pricing');
        $response->assertSee('/cart');

        // Verify removed default Filament widgets
        $response->assertDontSee('filament v5');
        $response->assertDontSee('Documentation');
    }
}
