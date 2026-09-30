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
        $response->assertSee('yoseph.iriandi.tambunan@gmail.com');
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
}
